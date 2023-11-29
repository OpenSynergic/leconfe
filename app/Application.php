<?php

namespace App;

use App\Actions\Site\SiteCreateAction;
use App\Models\Announcement;
use App\Models\Block;
use App\Models\Conference;
use App\Models\Navigation;
use App\Models\ParticipantPosition;
use App\Models\Scopes\ConferenceScope;
use App\Models\Site;
use App\Models\StaticPage;
use App\Models\Submission;
use App\Models\Timeline;
use App\Models\Topic;
use App\Models\Venue;
use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    public const APP_VERSION = '1.0.0';

    public const PHP_MIN_VERSION = '8.1';

    public const CONTEXT_WEBSITE = 0;

    protected ?Site $site;

    protected ?Conference $currentConference = null;

    public function isInstalled()
    {
        return file_exists(storage_path('installed'));
    }

    public function getAppVersion()
    {
        return static::APP_VERSION;
    }

    public function getPhpMinVersion()
    {
        return static::PHP_MIN_VERSION;
    }

    public function getCurrentConference(): ?Conference
    {
        return $this->currentConference;
    }

    public function setCurrentConference(Conference $conference)
    {
        $this->currentConference = $conference;
    }

    public function scopeCurrentConference(): void
    {
        foreach ([
            Submission::class,
            Topic::class,
            Venue::class,
            Navigation::class,
            Block::class,
            ParticipantPosition::class,
            Announcement::class,
            StaticPage::class,
            Timeline::class,
        ] as $model) {
            $model::addGlobalScope(new ConferenceScope);
        }
    }

    public function getNavigationItems(string $handle): array
    {
        return Navigation::query()
            ->where('conference_id', $this->getCurrentConference()?->getKey() ?? static::CONTEXT_WEBSITE)
            ->where('handle', $handle)
            ->first()?->items ?? [];
    }

    public function getSite(): Site
    {
        if (! isset($this->site)) {
            $this->site = Site::getSite() ?? SiteCreateAction::run();
        }

        return $this->site;
    }
}
