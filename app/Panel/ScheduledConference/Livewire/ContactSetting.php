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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ContactSetting extends Component implements HasForms, HasActions
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
                        Section::make(__('general.principal_contact'))
                            ->aside()
                            ->description(__('general.principal_contact_description'))
                            ->schema([
                                TextInput::make('meta.principal_contact_name')
                                    ->label(__('general.name'))
                                    ->required()
                                    ->autofocus(),
                                TextInput::make('meta.principal_contact_email')
                                    ->label(__('general.email'))
                                    ->required()
                                    ->type('email'),
                                TextInput::make('meta.principal_contact_phone')
                                    ->label(__('general.phone'))
                                    ->type('tel'),
                                TextInput::make('meta.principal_contact_affiliation')
                                    ->label(__('general.affiliation')),
                                Textarea::make('meta.mailing_address')
                                    ->label(__('general.mailing_address')),
                            ]),

                        Section::make(__('general.technical_support_contact'))
                            ->aside()
                            ->description(__('general.a_contact_person_who_can_assist_editors'))
                            ->schema([
                                TextInput::make('meta.support_contact_name')
                                    ->label(__('general.name'))
                                    ->required()
                                    ->autofocus(),
                                TextInput::make('meta.support_contact_email')
                                    ->label(__('general.email'))
                                    ->required()
                                    ->type('email'),
                                TextInput::make('meta.support_contact_phone')
                                    ->label(__('general.phone'))
                                    ->type('tel'),
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
