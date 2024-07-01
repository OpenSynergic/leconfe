<?php

namespace App\Panel\Conference\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DOISetup extends Component implements HasForms
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
                        Fieldset::make('DOIs')
                            ->schema([
                                Checkbox::make('meta.doi_enabled')
                                    ->label('Allow Digital Object Identifiers (DOIs) to be assigned to work published in this conference')
                            ])
                            ->columns(1),
                        Fieldset::make('Items with DOIs')
                            ->schema([
                                Placeholder::make('items.description')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('Select which items will be assigned a DOI.')),
                                CheckboxList::make('meta.doi_items')
                                    ->hiddenLabel()
                                    ->options([
                                        'articles' => 'Articles',
                                    ])
                            ])
                            ->columns(1),
                        TextInput::make('meta.doi_prefix')
                            ->label('DOI Prefix')
                            // ->maxWidth(MaxWidth::Small)
                            ->helperText(new HtmlString('The DOI Prefix is assigned by a registration agency, such as Crossref or DataCite. Example: 10.xxxx'))
                            ->placeholder('10.xxxxx')
                            ->regex('/^10\.\d+$/')
                            ->requiredUnless('meta.doi_enabled', true)
                            ->validationMessages([
                                'regex' => 'The DOI Prefix must be in the format 10.xxxx.',
                                'required_unless' => 'The DOI Prefix is required if DOIs are enabled.'
                            ]),
                        Select::make('meta.doi_automatic_assignment')
                            ->label('Automatic DOI Assignment')
                            ->helperText(new HtmlString('When should a submission be assigned a DOI?'))
                            ->placeholder('Never')
                            ->options([
                                'edit_stage' => 'Upon reaching editing stage',
                                'published' => 'Upon Publication',
                            ]),
                        Fieldset::make('DOI Format')
                            ->schema([
                                Radio::make('meta.doi_format')
                                    ->hiddenLabel()
                                    ->options([
                                        'default' => 'Default - Automatically generates a unique eight-character suffix',
                                        'none' => 'None - Suffixes must be entered manually on the DOI management page and will not be generated automatically'
                                    ])
                            ])
                            ->columns(1)
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
