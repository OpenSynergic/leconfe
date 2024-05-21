<?php

namespace App\Panel\Series\Livewire;


use App\Actions\Conferences\ConferenceUpdateAction;
use App\Actions\Series\SerieUpdateAction;
use App\Models\Enums\SerieType;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class InformationSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            ...app()->getCurrentSerie()->attributesToArray(),
        ]);
    }

    public function render()
    {
        return view('panel.conference.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(app()->getCurrentSerie())
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('Serie Title')
                            ->autofocus()
                            ->autocomplete()
                            ->required()
                            ->placeholder('Enter the title of the serie'),
                        TextInput::make('issn')
                            ->label('ISSN')
                            ->placeholder('Enter the ISSN of the serie'),
                        Grid::make([
                            'xl' => 2
                        ])
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->collection('thumbnail')
                                    ->helperText('An image representation of the serie that will be used in the list of series.')
                                    ->image()
                                    ->conversion('thumb'),
                                SpatieMediaLibraryFileUpload::make('cover')
                                    ->collection('cover')
                                    ->helperText('Cover image for the serie.')
                                    ->image()
                                    ->conversion('thumb'),
                            ]),
                        Grid::make()
                            ->schema([
                                DatePicker::make('date_start')
                                    ->label('Start Date')
                                    ->placeholder('Enter the start date of the serie')
                                    ->requiredWith('date_end'),
                                DatePicker::make('date_end')
                                    ->label('End Date')
                                    ->afterOrEqual('date_start')
                                    ->requiredWith('date_start')
                                    ->placeholder('Enter the end date of the serie'),
                            ]),
                        Select::make('type')
                            ->required()
                            ->options(SerieType::array()),
                        TinyEditor::make('meta.about')
                            ->label('About Serie')
                            ->minHeight(300),
                        TinyEditor::make('meta.additional_content')
                            ->minHeight(300),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                SerieUpdateAction::run(app()->getCurrentSerie(), $formData);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                                throw $th;
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }
}
