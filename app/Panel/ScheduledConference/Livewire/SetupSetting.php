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
use App\Models\Enums\UserRole;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Livewire\Component;
use Squire\Models\Country;

class SetupSetting extends Component implements HasForms, HasActions
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
                        Checkbox::make('meta.allow_registration')
                            ->label(__('general.allow_registration'))
                            ->helperText(__('general.allow_registration_helper')),
                        CheckboxList::make('meta.allowed_self_assign_roles')
                            ->label('Allow User to self register as role')
                            ->options(UserRole::selfAssignedRoleSetupNames()),
                        Select::make('meta.default_register_country')
                            ->label(__('general.default_register_country'))
                            ->placeholder(__('general.select_a_country'))
                            ->searchable()
                            ->options(fn() => Country::all()->mapWithKeys(fn($country) => [$country->id => $country->flag . ' ' . $country->name]))
                            ->optionsLimit(250),
                        Select::make('meta.timezone')
                            ->label(__('general.timezone'))
                            ->options(Timezonelist::toArray(false))
                            ->optionsLimit(500)
                            ->selectablePlaceholder(false)
                            ->searchable()
                            ->required(),
                    ]),
                Section::make(__('general.registration_required_fields'))
                    ->schema([
                        Checkbox::make('meta.required_given_name')
                            ->label(__('general.given_name')),
                        Checkbox::make('meta.required_family_name')
                            ->label(__('general.family_name')),
                        Checkbox::make('meta.required_public_name')
                            ->label(__('general.public_name')),
                        Checkbox::make('meta.required_affiliation')
                            ->label(__('general.affiliation')),
                        Checkbox::make('meta.required_country')
                            ->label(__('general.country')),
                        Checkbox::make('meta.required_phone')
                            ->label(__('general.phone')),
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
