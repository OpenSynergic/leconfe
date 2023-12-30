<?php

namespace App\Administration\Livewire;

use App\Actions\Settings\SettingUpdateAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ErrorReportSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount()
    {
        $this->form->fill([
            'send-error-report' => setting('send-error-report')
        ]);
    }

    public function render()
    {
        return view('administration.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('formData')
            ->schema([
                Section::make('Error Reporting')
                    ->description(new HtmlString(<<<'HTML'
                                        Sending report of technical problems helps us improve Leconfe.
                                    HTML))
                    ->schema([
                        Toggle::make('send-error-report')
                            ->label('Send error report to Leconfe'),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->successNotificationTitle('Saved!')
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                SettingUpdateAction::run($formData);

                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignLeft(),
            ]);
    }
}
