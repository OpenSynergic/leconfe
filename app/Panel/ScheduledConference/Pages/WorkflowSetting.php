<?php

namespace App\Panel\ScheduledConference\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Livewire;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Panel\Conference\Livewire\EmailSetting;
use App\Panel\Conference\Livewire\PublisherLibrary;
use App\Panel\ScheduledConference\Livewire\AuthorGuidance;
use App\Panel\ScheduledConference\Livewire\AuthorRoleTable;
use App\Panel\ScheduledConference\Livewire\EditorGuidance;
use App\Panel\ScheduledConference\Livewire\PresentationSetting;
use App\Panel\ScheduledConference\Livewire\ReviewFormTable;
use App\Panel\ScheduledConference\Livewire\ReviewGuidance;
use App\Panel\ScheduledConference\Livewire\ReviewSetupSetting;
use App\Panel\ScheduledConference\Livewire\SubmissionFileTypeTable;
use App\Panel\ScheduledConference\Livewire\SubmissionFormItemTable;
use App\Panel\ScheduledConference\Livewire\SubmissionSetting;
use App\Panel\ScheduledConference\Livewire\TopicTable;
use App\Panel\ScheduledConference\Livewire\TrackTable;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class WorkflowSetting extends Page
{
    protected string $view = 'panel.scheduledConference.pages.workflow-setting';

    public static function getNavigationGroup(): string
    {
        return __('general.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('general.workflow');
    }

    public function getHeading(): string|Htmlable
    {
        return __('general.workflow_settings');
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-window';

    public function mount(): void
    {
        $this->authorize('update', App::getCurrentScheduledConference());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->can('update', App::getCurrentScheduledConference());
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('workflow')
                    ->contained(false)
                    ->tabs([
                        Tab::make('Submission')
                            ->label(__('general.submissions'))
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make('workflow-submission')
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Submission')
                                            ->label(__('general.submission'))
                                            ->schema([
                                                Livewire::make(SubmissionSetting::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Components')
                                            ->label(__('general.components'))
                                            ->schema([
                                                Livewire::make(SubmissionFileTypeTable::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Form')
                                            ->label(__('general.form'))
                                            ->schema([
                                                Livewire::make(SubmissionFormItemTable::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Author Guidance')
                                            ->label(__('general.author_guidance'))
                                            ->schema([
                                                Livewire::make(AuthorGuidance::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Editor Guidance')
                                            ->label(__('general.editor_guidance'))
                                            ->schema([
                                                Livewire::make(EditorGuidance::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Author Roles')
                                            ->label(__('general.author_roles'))
                                            ->schema([
                                                Livewire::make(AuthorRoleTable::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Tracks')
                                            ->label(__('general.track'))
                                            ->schema([
                                                Livewire::make(TrackTable::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Topics')
                                            ->label(__('general.topic'))
                                            ->schema([
                                                Livewire::make(TopicTable::class),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Review')
                            ->label(__('general.review'))
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make('workflow-review')
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Setup')
                                            ->label(__('general.setup'))
                                            ->schema([
                                                Livewire::make(ReviewSetupSetting::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Reviewer Guidance')
                                            ->label(__('general.reviewer_guidance'))
                                            ->schema([
                                                Livewire::make(ReviewGuidance::class),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Review Form')
                                            ->label(__('scheduled_conference.review_form'))
                                            ->schema([
                                                Livewire::make(ReviewFormTable::class)
                                                    ->key('review_form_table'),
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Presentations')
                            ->label('Presentations')
                            ->schema([
                                Livewire::make(PresentationSetting::class),
                            ]),
                        Tab::make('Publisher Library')
                            ->label(__('general.publisher_library'))
                            ->schema([
                                Livewire::make(PublisherLibrary::class),
                            ]),
                        Tab::make('Emails')
                            ->label(__('general.email'))
                            ->schema([
                                Livewire::make(EmailSetting::class),
                            ]),
                    ]),
            ]);
    }
}
