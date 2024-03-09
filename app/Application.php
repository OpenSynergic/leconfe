<?php

namespace App;

use App\Actions\Site\SiteCreateAction;
use App\Models\Announcement;
use App\Models\Block;
use App\Models\Conference;
use App\Models\Navigation;
use App\Models\NavigationMenu;
use App\Models\ParticipantPosition;
use App\Models\PaymentItem;
use App\Models\Scopes\ConferenceScope;
use App\Models\Site;
use App\Models\StaticPage;
use App\Models\Submission;
use App\Models\Timeline;
use App\Models\Topic;
use App\Models\Venue;
use App\Models\Version;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Collection;

class Application extends LaravelApplication
{
    public const APP_VERSION = '1.0.0-beta.1';

    public const PHP_MIN_VERSION = '8.1';

    public const CONTEXT_WEBSITE = 0;

    protected int $currentConferenceId;

    protected string $currentConferencePath;

    public function isInstalled()
    {
        return config('app.installed');
    }

    public function getCodeVersion(): string
    {
        return static::APP_VERSION;
    }

    public function getVersion()
    {
        $version = new Version();
        $version->product_name = 'Leconfe';
        $version->product_folder = 'leconfe';
        $version->version = static::APP_VERSION;

        return $version;
    }

    public function getInstalledVersion(): string
    {
        return Version::application()?->version;
    }

    public function getPhpMinVersion()
    {
        return static::PHP_MIN_VERSION;
    }

    public function getCurrentConference(): ?Conference
    {
        return Conference::find($this->getCurrentConferenceId());
    }

    public function getCurrentConferenceId(): int
    {
        return $this->currentConferenceId ?? static::CONTEXT_WEBSITE;
    }

    public function setCurrentConferenceId(int $conferenceId)
    {
        $this->currentConferenceId = $conferenceId;
    }

    public function scopeCurrentConference(): void
    {
        foreach ([
            Submission::class,
            Topic::class,
            Venue::class,
            Navigation::class,
            NavigationMenu::class,
            Block::class,
            ParticipantPosition::class,
            Announcement::class,
            StaticPage::class,
            Timeline::class,
            PaymentItem::class,
        ] as $model) {
            $model::addGlobalScope(new ConferenceScope);
        }
    }

    public function getNavigationItems(string $handle): Collection
    {
        return NavigationMenu::query()
            ->where('handle', $handle)
            ->with([
                'items' => function ($query) {
                    $query
                        ->ordered()
                        ->whereNull('parent_id')
                        ->with('children', function ($query) {
                            $query->ordered();
                        });
                }
            ])
            ->first()?->items ?? collect();
    }

    public function getSite(): Site
    {
        return Site::getSite() ?? SiteCreateAction::run();
    }

    public function isReportingErrors(): bool
    {
        try {
            if ($this->isProduction() && ! $this->hasDebugModeEnabled() && setting('send-error-report', true)) {
                return true;
            }
        } catch (\Throwable $th) {
            //
        }

        return false;
    }
}
