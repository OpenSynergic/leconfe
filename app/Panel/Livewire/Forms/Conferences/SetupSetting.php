<?php

namespace App\Panel\Livewire\Forms\Conferences;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Panel;

class SetupSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public Conference $conference;

    public ?array $formData = [];

    // public function panel(Panel $panel): Panel
    // {
    //     return $panel
    //         // ...
    //         ->favicon(asset('logo.png'));
    // }

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
                                    ->image()
                                    ->conversion('thumb')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                        SpatieMediaLibraryFileUpload::make('favicon')
                                    ->collection('favicon')
                                    ->image()
                                    ->imageResizeUpscale(false)
                                    ->conversion('thumb')
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
                                    ]),
                        SpatieMediaLibraryFileUpload::make('styleSheet')
                                    ->collection('styleSheet')
                                    ->preserveFilenames()
                                    // ->acceptedFileTypes(['text/css'])
                                    ->columnSpan([
                                        'xl' => 1,
                                        'sm' => 2,
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
