<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

use function Laravel\Prompts\spin;

class CheckLatestVersion
{
    use AsAction;

    public function handle()
    {
        return Cache::remember('get_latest_version', now()->addHour(6), fn () => $this->getLatestVersion());
    }

    public static function isUpdateAvailable()
    {
        $data = static::run();

        return version_compare(app()->getInstalledVersion(), $data['tag'], '<');
    }

    public function getLatestVersion()
    {
        $response = Http::withHeaders([
            'Leconfe-Telemetry-Client' => '1',
        ])->get(app()->getApiUrl('checkversion'));

        if ($response->failed()) {
            throw new \Exception('Failed to get latest version');
        }

        return $response->json();
    }

    public function asCommand(Command $command): void
    {
        try {
            $data = spin(
                message: 'Getting Leconfe latest version...',
                callback: fn () => $this->handle(),
            );

            $command->table(
                ['Current Version', 'Latest Version'],
                [
                    [app()->getInstalledVersion(), $data['tag']],
                ]
            );

            if (version_compare(app()->getInstalledVersion(), $data['tag'], '>=')) {
                $command->info('Your application is already up to date!');
            } else {
                $command->info('New version available : '.$data['tag']);
                $command->info('Download latest version here : '.$data['package']);
                $command->warn('Learn how to upgrade here: '.$data['upgrade_guide']);
            }
        } catch (\Throwable $th) {
            throw new $th;
            $command->error($th->getMessage());
        }
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:check-latest-version';
    }

    public function getCommandDescription(): string
    {
        return 'Check leconfe version';
    }
}
