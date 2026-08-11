<?php

namespace App\Actions\Leconfe;

use App\Models\Enums\UserRole;
use App\Models\User;
use App\Services\Telemetry\TelemetrySettings;
use App\Services\Telemetry\TelemetrySnapshotBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class SendTelemetry
{
    use AsAction;

    private const DISPATCH_KEY_PREFIX = 'leconfe-telemetry-dispatch-';

    private const DISPATCH_MARKER_TTL = 86400;

    public function __construct(
        private readonly TelemetrySettings $settings,
        private readonly TelemetrySnapshotBuilder $snapshotBuilder,
    ) {}

    /**
     * @return array{status: string, error?: string}
     */
    public function handle(): array
    {
        if (! app()->isInstalled()) {
            return ['status' => 'not-installed'];
        }

        if (! $this->settings->enabled()) {
            return ['status' => 'disabled'];
        }

        $today = now('UTC')->toDateString();
        $lock = Cache::lock('leconfe-telemetry-'.$today, 600);

        if (! $lock->get()) {
            return ['status' => 'skipped'];
        }

        try {
            if ($this->settings->lastSendDate() === $today) {
                return ['status' => 'skipped'];
            }

            $this->settings->markAttempt($today);
            $token = $this->settings->token() ?: $this->register();
            if (! $token) {
                throw new \RuntimeException('Telemetry registration did not return an installation token.');
            }

            $response = Http::timeout(5)
                ->withToken($token)
                ->post(app()->getApiUrl('v1/installations/snapshots'), $this->snapshotBuilder->build());

            if ($response->failed()) {
                throw new \RuntimeException('Telemetry snapshot request failed with HTTP '.$response->status().'.');
            }

            $this->settings->record('sent');
            Log::info('Leconfe telemetry send completed.', ['status' => 'sent']);

            return ['status' => 'sent'];
        } catch (\Throwable $exception) {
            $this->settings->record('failed', $exception->getMessage());
            Cache::forget(self::dispatchKey($today));
            Log::warning('Leconfe telemetry send failed.', ['status' => 'failed']);

            return [
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ];
        } finally {
            optional($lock)->release();
        }
    }

    public static function dispatchOncePerDay(): void
    {
        $settings = app(TelemetrySettings::class);

        if (! app()->isInstalled() || ! $settings->enabled()) {
            return;
        }

        $today = now('UTC')->toDateString();

        if ($settings->lastSendDate() === $today) {
            return;
        }

        $key = self::dispatchKey($today);

        if (! Cache::add($key, true, self::DISPATCH_MARKER_TTL)) {
            return;
        }

        try {
            self::dispatch();
        } catch (\Throwable $exception) {
            Cache::forget($key);

            throw $exception;
        }
    }

    private static function dispatchKey(string $date): string
    {
        return self::DISPATCH_KEY_PREFIX.$date;
    }

    private function register(): ?string
    {
        $admin = User::withoutGlobalScopes()
            ->whereHas('roles', fn ($query) => $query->where('name', UserRole::Admin->value))
            ->first();
        $response = Http::timeout(5)->post(app()->getApiUrl('v1/installations/register'), [
            'installation_id' => $this->settings->installationId() ?: app()->getUniqueIdentifier(),
            'url' => url(''),
            'version' => app()->getInstalledVersion(),
            'admin_name' => $admin?->full_name,
            'admin_email' => $admin?->email,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Telemetry registration failed with HTTP '.$response->status().'.');
        }

        $token = $response->json('token');
        if (! is_string($token) || $token === '') {
            return null;
        }

        $installationId = $response->json('installation_id');
        if (is_string($installationId) && $installationId !== '') {
            $this->settings->storeInstallationId($installationId);
        }

        $this->settings->storeToken($token);

        return $token;
    }
}
