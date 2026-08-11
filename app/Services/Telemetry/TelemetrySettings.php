<?php

namespace App\Services\Telemetry;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class TelemetrySettings
{
    public const ENABLED = 'telemetry_enabled';

    public const INSTALLATION_TOKEN = 'telemetry_installation_token';

    public const INSTALLATION_ID = 'telemetry_installation_id';

    public const LAST_ATTEMPT_DATE = 'telemetry_last_attempt_date';

    public const LAST_ATTEMPT_AT = 'telemetry_last_attempt_at';

    public const LAST_ATTEMPT_STATUS = 'telemetry_last_attempt_status';

    public const LAST_ATTEMPT_ERROR = 'telemetry_last_attempt_error';

    public const LAST_SEND_AT = 'telemetry_last_send_at';

    public const UPGRADE_NOTICE_PENDING = 'telemetry_upgrade_notice_pending';

    public const UPGRADE_NOTICE_SHOWN = 'telemetry_upgrade_notice_shown';

    public function enabled(): bool
    {
        return (bool) $this->getSiteSetting(self::ENABLED, (bool) config('app.beacon', true))
            && (bool) config('app.beacon', true);
    }

    public function setEnabled(bool $enabled): void
    {
        $this->setSiteSetting(self::ENABLED, $enabled);
    }

    public function token(): ?string
    {
        $encrypted = $this->getSiteSetting(self::INSTALLATION_TOKEN);

        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    public function storeToken(string $token): void
    {
        $this->setSiteSetting(self::INSTALLATION_TOKEN, Crypt::encryptString($token));
    }

    public function installationId(): ?string
    {
        $encrypted = $this->getSiteSetting(self::INSTALLATION_ID);

        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        try {
            $installationId = Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return null;
        }

        return $installationId !== '' ? $installationId : null;
    }

    public function storeInstallationId(string $installationId): void
    {
        $this->setSiteSetting(self::INSTALLATION_ID, Crypt::encryptString($installationId));
    }

    public function lastAttemptDate(): ?string
    {
        $date = $this->getSiteSetting(self::LAST_ATTEMPT_DATE);

        return $date instanceof Carbon ? $date->toDateString() : (is_string($date) ? $date : null);
    }

    public function markAttempt(string $date): void
    {
        $this->setSiteSetting(self::LAST_ATTEMPT_DATE, $date);
        $this->setSiteSetting(self::LAST_ATTEMPT_AT, now()->toISOString());
        $this->setSiteSetting(self::LAST_ATTEMPT_STATUS, 'sending');
        $this->setSiteSetting(self::LAST_ATTEMPT_ERROR, null);
    }

    public function lastSendDate(): ?string
    {
        $sentAt = $this->getSiteSetting(self::LAST_SEND_AT);

        if ($sentAt instanceof Carbon) {
            return $sentAt->utc()->toDateString();
        }

        if (! is_string($sentAt) || $sentAt === '') {
            return null;
        }

        try {
            return Carbon::parse($sentAt)->utc()->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    public function record(string $status, ?string $error = null): void
    {
        $now = now()->toISOString();

        $this->setSiteSetting(self::LAST_ATTEMPT_AT, $now);
        $this->setSiteSetting(self::LAST_ATTEMPT_STATUS, $status);
        $this->setSiteSetting(self::LAST_ATTEMPT_ERROR, $error ? mb_substr($error, 0, 500) : null);

        if ($status === 'sent') {
            $this->setSiteSetting(self::LAST_SEND_AT, $now);
        }
    }

    public function upgradeNoticePending(): bool
    {
        return (bool) $this->getSiteSetting(self::UPGRADE_NOTICE_PENDING, false);
    }

    public function upgradeNoticeShown(): bool
    {
        return (bool) $this->getSiteSetting(self::UPGRADE_NOTICE_SHOWN, false);
    }

    public function queueUpgradeNotice(): void
    {
        $this->setSiteSetting(self::UPGRADE_NOTICE_PENDING, true);
    }

    public function markUpgradeNoticeShown(): void
    {
        $this->setSiteSetting(self::UPGRADE_NOTICE_PENDING, false);
        $this->setSiteSetting(self::UPGRADE_NOTICE_SHOWN, true);
    }

    /**
     * @return array{status: ?string, attempted_at: ?string, sent_at: ?string, error: ?string}
     */
    public function status(): array
    {
        return [
            'status' => $this->getSiteSetting(self::LAST_ATTEMPT_STATUS),
            'attempted_at' => $this->getSiteSetting(self::LAST_ATTEMPT_AT),
            'sent_at' => $this->getSiteSetting(self::LAST_SEND_AT),
            'error' => $this->getSiteSetting(self::LAST_ATTEMPT_ERROR),
        ];
    }

    private function getSiteSetting(string $key, mixed $default = null): mixed
    {
        return app()->getSite()->getMeta('settings_'.$key) ?? $default;
    }

    private function setSiteSetting(string $key, mixed $value): void
    {
        app()->getSite()->setMeta('settings_'.$key, $value);
    }
}
