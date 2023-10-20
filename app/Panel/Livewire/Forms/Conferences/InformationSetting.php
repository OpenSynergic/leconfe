<?php

namespace App\Panel\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use App\Models\Information;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Panel;
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
        return view('panel.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->conference)
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                TextInput::make('path')
                                    ->rule('alpha_dash')
                                    ->required()
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                TextInput::make('meta.location')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                Flatpickr::make('meta.date_held')
                                    ->rule('date')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->collection('logo')
                                    ->image()
                                    ->imageResizeUpscale(false)
                                    ->conversion('thumb')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->collection('thumbnail')
                                    ->helperText('A image representation of the conference that can be used in lists of conferences.')
                                    ->image()
                                    ->conversion('thumb')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                                Textarea::make('meta.description')
                                    ->rows(5)
                                    ->autosize()
                                    ->columnSpanFull(),
                                TinyEditor::make('meta.about')
                                    ->label('About Conference')
                                    ->minHeight(300)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),
                                TinyEditor::make('meta.page_footer')
                                    ->minHeight(300)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),
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
