<?php

namespace App\Panel\Conference\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DOIRegistration extends Component implements HasForms
{
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
        return view('panel.conference.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(app()->getCurrentConference())
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('meta.doi_registration_agency')
                            ->label("Registration Agency")
                            ->helperText('Please select the registration agency you would like to use when depositing DOIs.')
                            ->reactive()

                            ->options([
                                'crossref' => 'Crossref',
                            ]),
                        Grid::make(1)
                            ->hidden(fn (Get $get) => !$get('meta.doi_registration_agency'))
                            ->schema([
                                Section::make('Automatic Deposit')
                                    ->schema([
                                        Placeholder::make('doi_automatic_deposit_description')
                                            ->content("The DOI registration and metadata can be automatically deposited with the selected registration agency whenever an item with a DOI is published. Automatic deposit will happen at scheduled intervals and each DOI's registration status can be monitored from the DOI management page")
                                            ->hiddenLabel(),
                                        Checkbox::make('meta.doi_automatic_deposit')
                                            ->label('Automatically deposit DOIs')
                                    ]),
                                Placeholder::make('Crossref Settings')
                                    ->content("The following items are required for a successful Crossref deposit"),
                                TextInput::make('meta.doi_crossref_depositor_name')
                                    ->label("Depositor Name")
                                    ->helperText("Name of the organization registering the DOIs. It is included with deposited metadata and used to record who submitted the deposit.")
                                    ->required(),
                                TextInput::make('meta.doi_crossref_depositor_email')
                                    ->label("Depositor Email")
                                    ->helperText("Email address of the individual responsible for registering content with Crossref. It is included with the deposited metadata and used when sending the deposit confirmation email.")
                                    ->required(),
                                Placeholder::make('information')
                                    ->hiddenLabel()
                                    ->content(new HtmlString(<<<HTML
                                        <div class="prose prose-sm max-w-none">
                                            <p>If you would like to register Digital Object Identifiers (DOIs) directly with <a href="http://www.crossref.org/">Crossref</a>, you will need to add your <a href="https://www.crossref.org/documentation/member-setup/account-credentials/">Crossref account credentials</a> into the username and password fields below.</p>
                                            <p>Depending on your Crossref membership, there are two ways to enter your username and password:</p>
                                            <ul>
                                                <li>If you are using an organizational account, add your <a href="https://www.crossref.org/documentation/member-setup/account-credentials/#00376">shared username and password</a></li>
                                                <li>If you are using a <a href="https://www.crossref.org/documentation/member-setup/account-credentials/#00368">personal account</a>, enter your email address and the role in the username field. The username will look like: email@example.com/role</li>
                                                <li>If you do not know or have access to your Crossref credentials, you can contact <a href="https://support.crossref.org/">Crossref support</a> for assistance. Without credentials, you may still export metadata into the Crossref XML format, but you cannot register your DOIs with Crossref from Leconfe.</li>
                                            </ul>
                                        </div>
                                    HTML)),
                                TextInput::make('meta.doi_crossref_username')
                                    ->label("Username")
                                    ->helperText('The Crossref username that will be used to authenticate your deposits. If you are using a personal account, please see the advice above.'),
                                TextInput::make('meta.doi_crossref_password')
                                    ->label("Password")
                                    ->password()
                                    ->revealable()
                                    ->required(),
                                Checkbox::make('meta.doi_crossref_test')
                                    ->label('Use the Crossref test API (testing environment) for the DOI deposit.')
                                    ->inline()
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
                                ConferenceUpdateAction::run(app()->getCurrentConference(), $formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                throw $th;
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
