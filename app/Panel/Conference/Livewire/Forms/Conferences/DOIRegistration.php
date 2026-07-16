<?php

namespace App\Panel\Conference\Livewire\Forms\Conferences;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\DOIRegistrationFacade;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;
use Livewire\Component;

class DOIRegistration extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'meta' => app()->getCurrentConference()->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(app()->getCurrentConference())
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('meta.doi_registration_agency')
                            ->label(__('general.registration_agency'))
                            ->helperText(__('general.registration_select_agency_dois'))
                            ->reactive()
                            ->options(DOIRegistrationFacade::getAllDriverNames()),
                        Grid::make(1)
                            ->hidden(fn (Get $get) => ! $get('meta.doi_registration_agency'))
                            ->schema(function (Get $get) {
                                if (! $get('meta.doi_registration_agency')) {
                                    return [];
                                }

                                return DOIRegistrationFacade::driver($get('meta.doi_registration_agency'))?->getSettingFormSchema() ?? [];
                            }),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();

                            try {
                                ConferenceUpdateAction::run(app()->getCurrentConference(), $formData);

                                if (Arr::get($formData, 'meta.doi_registration_agency')) {

                                    $driver = DOIRegistrationFacade::driver(Arr::get($formData, 'meta.doi_registration_agency'));
                                    $driver->updateSettings($formData);
                                }

                                $action->sendSuccessNotification();
                            } catch (Throwable $th) {
                                throw $th;
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
