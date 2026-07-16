<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\ScheduledConferences\ScheduledConferenceUpdateAction;
use App\Forms\Components\TinyEditor;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Stevebauman\Purify\Facades\Purify;

class AppearanceSetupSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $scheduledConference = app()->getCurrentScheduledConference();

        $this->form->fill([
            ...$scheduledConference->attributesToArray(),
            'meta' => $scheduledConference->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(app()->getCurrentScheduledConference())
            ->schema([
                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->label(__('general.logo'))
                            ->image()
                            ->imageResizeUpscale(false)
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->collection('favicon')
                            ->label('Favicon')
                            ->image()
                            ->imageResizeUpscale(false)
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label(__('general.scheduled_conference_thumbnail'))
                            ->collection('thumbnail')
                            ->helperText(__('general.image_representation_of_the_serie_will_uses'))
                            ->image()
                            ->conversion('thumb'),
                        TinyEditor::make('meta.page_footer')
                            ->label(__('general.page_footer'))
                            ->profile('advanced')
                            ->minHeight(300)
                            ->dehydrateStateUsing(fn (?string $state) => Purify::clean($state)),
                    ]),

                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                ScheduledConferenceUpdateAction::run(app()->getCurrentScheduledConference(), $formData);
                                $action->sendSuccessNotification();
                            } catch (Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),

            ])
            ->statePath('formData');
    }
}
