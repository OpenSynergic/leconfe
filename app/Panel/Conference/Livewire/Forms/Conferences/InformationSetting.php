<?php

namespace App\Panel\Conference\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
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

    public Conference $conference;

    public ?array $formData = [];

    public function mount(Conference $conference): void
    {
        $this->form->fill([
            ...$conference->attributesToArray(),
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
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->required(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('meta.acronym')
                                    ->unique(column: 'path', ignorable: $this->conference)
                                    ->rule('alpha_dash')
                                    ->live(onBlur: true),
                                Placeholder::make('path')
                                    ->content(function (Get $get) {
                                        $baseUrl = config('app.url') . '/';
                                        $acronym = $get('meta.acronym') ?? '{acronym}';
                                        return new HtmlString("<span class='text-gray-500'>{$baseUrl}</span>{$acronym}");
                                    }),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('date_start'),
                                DatePicker::make('date_end')
                                    ->after('date_start'),
                            ]),
                        Grid::make([
                            'xl' => 3
                        ])
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->collection('logo')
                                    ->image()
                                    ->imageResizeUpscale(false)
                                    ->conversion('thumb'),
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->collection('thumbnail')
                                    ->helperText('A image representation of the conference that can be used in lists of conferences.')
                                    ->image()
                                    ->conversion('thumb'),
                                SpatieMediaLibraryFileUpload::make('cover')
                                    ->collection('cover')
                                    ->helperText('Cover image for the conference. This will be used in the conference\'s branding.')
                                    ->image()
                                    ->conversion('thumb'),
                            ]),

                        TextInput::make('meta.theme')
                            ->placeholder('e.g. Creating a better future with us')
                            ->helperText("The theme of the conference. This will be used in the conference's branding.")
                            ->columnSpanFull(),
                        Textarea::make('meta.description')
                            ->rows(5)
                            ->autosize()
                            ->columnSpanFull(),
                        TinyEditor::make('meta.about')
                            ->label('About Conference')
                            ->minHeight(300),
                        TinyEditor::make('meta.additional_content')
                            ->minHeight(300),
                        TinyEditor::make('meta.page_footer')
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
