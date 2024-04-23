<?php

namespace App\Panel\Administration\Livewire;

use App\Actions\Site\SiteUpdateAction;
use App\Facades\Settings;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Stevebauman\Purify\Facades\Purify;

class InformationSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount()
    {
        $this->form->fill(setting()->all());
    }

    public function render()
    {
        return view('panel.administration.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('settings.name')
                            ->label('Website Name')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->model(app()->getSite())
                            ->image()
                            ->imageResizeUpscale(false)
                            ->conversion('thumb')
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        Textarea::make('meta.description')
                            ->rows(3)
                            ->autosize()
                            ->columnSpanFull(),
                        TinyEditor::make('meta.about')
                            ->label('About Site')
                            ->minHeight(300)
                            ->dehydrateStateUsing(fn (string $state) => Purify::clean($state))
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        TinyEditor::make('meta.page_footer')
                            ->minHeight(300)
                            ->dehydrateStateUsing(fn (string $state) => Purify::clean($state))
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                    ])
                    ->columns(2),
                Actions::make([
                    Action::make('save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Failed!')
                        ->action(function (Action $action) {
                            $data = $this->form->getState();
                            try {
                                SiteUpdateAction::run($data);
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ]),
            ])
            ->statePath('formData');
    }
}
