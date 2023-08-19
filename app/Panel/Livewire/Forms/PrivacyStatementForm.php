<?php

namespace App\Panel\Livewire\Forms;

use App\Actions\Settings\SettingUpdateAction;
use App\Panel\Livewire\Traits\PlaceholderTrait;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class PrivacyStatementForm extends Component implements HasForms
{
    use InteractsWithForms, PlaceholderTrait;

    public $data;

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        $this->form->fill([
            'privacy_statement' => setting('privacy_statement'),
        ]);
    }

    public function render()
    {
        return view('panel.livewire.forms.privacy-statement');
    }

    protected function getFormSchema(): array
    {
        return [
            RichEditor::make('privacy_statement')->label(''),
        ];
    }

    public function submit()
    {
        SettingUpdateAction::run($this->form->getState());

        Notification::make('setting_saved')
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
