<?php

namespace App\Http\Livewire\Forms;

use App\Actions\Settings\UpdateSettingsAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PrivacyStatement extends Component implements HasForms
{
    use InteractsWithForms;

    public $data;

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        $this->form->fill([
            'privacy_statement' => setting('privacy_statement')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.privacy-statement');
    }

    protected function getFormSchema(): array
    {

        return [
            RichEditor::make('privacy_statement')
                ->label('')
        ];
    }

    public function submit()
    {

        UpdateSettingsAction::run($this->form->getState());

        Notification::make('setting_saved')
            ->title("Settings saved")
            ->success()
            ->send();
    }
}
