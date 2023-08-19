<?php

namespace App\Panel\Livewire\Forms\Website;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class GeneralSettingForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Conference $record;

    public function mount(): void
    {
        $this->form->fill([
            $this->record->attributesToArray(),
            'meta' => $this->record->getAllMeta(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logo')
                    ->image()
                    ->conversion('thumb'),
                TinyEditor::make('meta.page_footer'),

            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function submit(): void
    {
        ConferenceUpdateAction::run($this->form->getState(), $this->record);

        Notification::make('setting_saved')
            ->title('Settings saved')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('panel.livewire.forms.website.general-setting-form');
    }
}
