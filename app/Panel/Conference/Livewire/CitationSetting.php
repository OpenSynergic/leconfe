<?php

namespace App\Panel\Conference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\Citation;
use App\Models\AuthorRole;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class CitationSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            ...app()->getCurrentConference()->attributesToArray(),
            'meta' => app()->getCurrentConference()->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        $citationStyleOptions = collect(Citation::getCitationStyles())->mapWithKeys(fn ($style) => [$style['id'] => $style['title']]);
        $citationDownloadOptions = collect(Citation::getCitationDownloads())->mapWithKeys(fn ($style) => [$style['id'] => $style['title']]);

        return $schema
            ->model(app()->getCurrentConference())
            ->schema([
                Section::make()
                    ->schema([
                        Radio::make('meta.primary_citation_format')
                            ->label(__('general.primary_citation_format'))
                            ->options($citationStyleOptions),
                        CheckboxList::make('meta.enabled_citation_styles')
                            ->required()
                            ->label(__('general.enabled_citation_styles'))
                            ->options($citationStyleOptions),
                        CheckboxList::make('meta.downloadable_citation_formats')
                            ->helperText(__('general.downloadable_citation_formats_helper'))
                            ->label(__('general.downloadable_citation_formats'))
                            ->options($citationDownloadOptions),

                        Section::make('Contributors')
                            ->description(__('general.citation_contributors_description'))
                            ->schema([
                                CheckboxList::make('meta.citation_contributor_authors')
                                    ->label(__('general.authors'))
                                    ->helperText(__('general.citation_contributor_authors_helper'))
                                    ->options(AuthorRole::pluck('name', 'id')->toArray()),
                                CheckboxList::make('meta.citation_contributor_translators')
                                    ->label(__('general.translators'))
                                    ->helperText(__('general.citation_contributor_translator_helper'))
                                    ->options(AuthorRole::pluck('name', 'id')->toArray()),

                            ]),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                ConferenceUpdateAction::run($this->form->getRecord(), $formData);
                            } catch (Throwable $th) {
                                $action->sendFailureNotification();
                                $action->halt();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
