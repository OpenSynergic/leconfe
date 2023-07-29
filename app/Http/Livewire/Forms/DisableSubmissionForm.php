<?php

namespace App\Http\Livewire\Forms;

use App\Actions\Settings\UpdateSettingsAction;
use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Rawilk\Settings\Facades\Settings;

class DisableSubmissionForm extends Component implements HasForms
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
            'disable_submission' => setting('disable_submission')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.disable-submission-form');
    }

    protected function getFormSchema(): array
    {
        return [
            Placeholder::make('disable_submission_label')
                ->label('')
                ->content('Prevent user from submitting new paper to conferences.'),
            Toggle::make('disable_submission')
                ->label('Disable Submission')
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
