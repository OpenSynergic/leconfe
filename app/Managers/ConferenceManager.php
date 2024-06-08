<?php

namespace App\Managers;

use App\Application;
use App\Models\Announcement;
use App\Models\AuthorRole;
use App\Models\Committee;
use App\Models\CommitteeRole;
use App\Models\Conference;
use App\Models\MailTemplate;
use App\Models\NavigationMenu;
use App\Models\PaymentItem;
use App\Models\Proceeding;
use App\Models\Scopes\ConferenceScope;
use App\Models\Scopes\SerieScope;
use App\Models\Serie;
use App\Models\SpeakerRole;
use App\Models\Sponsor;
use App\Models\StaticPage;
use App\Models\Submission;
use App\Models\Timeline;
use App\Models\Topic;
use App\Models\Venue;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class ConferenceManager {

    protected ?Conference $currentConference = null;

    protected ?Serie $currentSerie = null;

    protected bool $booted = false;

    public function __construct()
    {   
        $this->boot();
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }
        
        $this->detectConference();
        
        $this->booted = true;
    }

    public function getCurrentConference(): ?Conference
    {
        return $this->currentConference;
    }

    public function getCurrentConferenceId(): int
    {
        return $this->getCurrentConference()?->getKey() ?? Application::CONTEXT_WEBSITE;
    }

    public function setCurrentConference(Conference $conference)
    {
        $this->currentConference = $conference;
    }

    public function getCurrentSerieId(): ?int
    {
        return $this->currentSerie?->getKey();
    }

    public function getCurrentSerie(): ?Serie
    {
        return $this->currentSerie;
    }

    public function setCurrentSerie(Serie $serie)
    {
        $this->currentSerie = $serie;
    }

    public function scopeCurrentSerie(): void
    {
        $models = [
            Venue::class,
            Timeline::class,
            CommitteeRole::class,
            SpeakerRole::class,
            Sponsor::class,
            Committee::class,
        ];

        foreach ($models as $model) {
            $model::addGlobalScope(new SerieScope);
        }
    }

    public function detectConference(){
        $pathInfos = explode('/', request()->getPathInfo());
        // Detect conference from URL path
        if (isset($pathInfos[1]) && !blank($pathInfos[1])) {
            
            $conference = Conference::query()
                ->with(['media', 'meta', 'currentSerie'])
                ->where('path', $pathInfos[1])->first();

                
            // Detect serie from URL path when conference is set
            if ($conference) {
                $this->setCurrentConference($conference);
                
                if(isset($pathInfos[3]) && !blank($pathInfos[3])){
                    $serie = Serie::where('path', $pathInfos[3])->first();
                }

                $serie ??= $conference->currentSerie;
                if ($serie) {
                    $this->setCurrentSerie($serie);
                    $this->scopeCurrentSerie();
                }

                $this->scopeCurrentConference();

                // if($conference){
                //     Livewire::setUpdateRoute(
                //         fn ($handle) => Route::post('{conference:path}/series/{serie:path}/livewire/update', $handle)->middleware('web')
                //     );
                // }
            }
        }

        setPermissionsTeamId($this->getCurrentConferenceId());
    }

    public function scopeCurrentConference(): void
    {
        $models = [
            Submission::class,
            Topic::class,
            NavigationMenu::class,
            AuthorRole::class,
            Announcement::class,
            StaticPage::class,
            PaymentItem::class,
            Serie::class,
            Proceeding::class,
            MailTemplate::class,
        ];

        foreach ($models as $model) {
            $model::addGlobalScope(new ConferenceScope);
        }
    }
}