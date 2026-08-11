<?php

namespace App\Frontend\Website\Pages;

use App\Facades\MetaTag;
use App\Services\Telemetry\TelemetrySettings;

class InstallationSuccessful extends Page
{
    protected static string $view = 'frontend.website.pages.installation-successful';

    public bool $showTelemetryNotice = false;

    /**
     * @var array{status: ?string, attempted_at: ?string, sent_at: ?string, error: ?string}
     */
    public array $telemetryStatus = [];

    public function mount()
    {
        MetaTag::add('robots', 'noindex, nofollow');

        $this->telemetryStatus = app(TelemetrySettings::class)->status();
        $telemetrySettings = app(TelemetrySettings::class);
        $this->showTelemetryNotice = auth()->check()
            && ! $telemetrySettings->upgradeNoticeShown()
            && $telemetrySettings->upgradeNoticePending();
        if ($this->showTelemetryNotice) {
            $telemetrySettings->markUpgradeNoticeShown();
        }
    }

    public static function getLayout(): string
    {
        return 'frontend.website.components.layouts.base';
    }
}
