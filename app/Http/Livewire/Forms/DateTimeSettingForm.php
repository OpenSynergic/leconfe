<?php

namespace App\Http\Livewire\Forms;

use App\Actions\Settings\UpdateSettingsAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;


// TODO tambahkan custom format field, dan juga lakukan validasi bahwa format yang diinput valid
class DateTimeSettingForm extends Component implements HasForms
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
            'format' => setting('format')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.date-time-setting-form');
    }

    protected function getFormSchema(): array
    {
        $now = now()->hours(16);

        return [
            Radio::make('format.date')
                ->options(fn () => collect([
                    'F j, Y',
                    'F j Y',
                    'j F Y',
                    'Y F j',
                ])->mapWithKeys(fn ($format) => [$format => $now->format($format)])),
            Radio::make('format.time')
                ->options(fn () => collect([
                    'h:i A',
                    'g:ia',
                    'H:i',
                ])->mapWithKeys(fn ($format) => [$format => $now->format($format)])),
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
