<?php

namespace App\Panel\Conference\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ContactSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public Conference $conference;

    public ?array $formData = [];

    public function mount(Conference $conference): void
    {
        $this->form->fill([
            'meta' => $conference->getAllMeta(),
        ]);
    }

    public function render()
    {
        return view('panel.conference.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->conference)
            ->schema([
                Section::make()
                    ->schema([
                        Section::make('')
                            ->schema([
                                TextInput::make('meta.email')
                                    ->email()
                                    ->placeholder('example@gmail.com')
                                    ->required()
                                    ->helperText(__('Primary contact email for the contact information')),
                                TextInput::make('meta.address')
                                    ->helperText(__('The physical location associated with the your company or organization')),
                            ])
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ]),
                        Section::make('')
                            ->schema([
                                TextInput::make('meta.phone')
                                    ->rules([Rule::phone()->international()])
                                    ->placeholder('International format, e.g. +6281234567890')
                                    ->helperText(__('International phone number along with the country code')),
                                TextInput::make('meta.bussines_hour')
                                    ->label(__('Bussines Hour'))
                                    ->placeholder(__('Mon-Fri from 8am to 5pm')),
                            ])
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ]),
                        Section::make('')
                            ->schema([
                                TextInput::make('meta.whatsapp')
                                    ->rules([Rule::phone()->international()])
                                    ->placeholder('International format, e.g. +6281234567890')
                                    ->helperText(__('Automaticly generate a clickable link to your whatsapp')),
                                TextInput::make('meta.label_chat')
                                    ->label(__('Chat label'))
                                    ->placeholder('Start new chat')
                                    ->helperText(__('This will be the clickable title of the link.')),
                            ])
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ]),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                ConferenceUpdateAction::run($this->conference, $formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
