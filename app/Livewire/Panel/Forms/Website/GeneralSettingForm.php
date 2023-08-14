<?php

namespace App\Livewire\Panel\Forms\Website;

use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Livewire\Component;
use Illuminate\Contracts\View\View;

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
                RichEditor::make('meta.page_footer')

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
        return view('livewire.panel.forms.website.general-setting-form');
    }
}
