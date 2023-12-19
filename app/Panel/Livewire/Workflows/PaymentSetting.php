<?php

namespace App\Panel\Livewire\Workflows;

use App\Facades\Payment;
use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Squire\Models\Currency;

class PaymentSetting extends WorkflowStage implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected ?string $stage = 'payment';

    protected ?string $stageLabel = 'Payment';

    public array $data = [];

    public function mount()
    {
        $this->form->fill([
            'payment' => [
                'enabled' => $this->conference->getMeta('payment.enabled'),
                'method' => $this->conference->getMeta('payment.method', 'manual'),
                'supported_currencies' => $this->conference->getMeta('payment.supported_currencies', ['usd']),
            ],
            ...Payment::getAllDriverNames()->map(fn ($name, $key) => Payment::driver($key)->getSettingFormFill())->toArray(),
        ]);
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->label('Save')
            ->icon('lineawesome-save-solid')
            ->failureNotificationTitle('Save Failed')
            ->successNotificationTitle('Saved')
            ->action(function (Action $action) {
                $this->form->validate();

                try {
                    $data = $this->form->getState();
                    
                    foreach ($data['payment'] as $key => $value) {
                        $this->conference->setMeta('payment.' . $key, $value);
                    }

                    Payment::driver($this->conference->getMeta('payment.method'))
                        ->saveSetting(data_get($data, $this->conference->getMeta('payment.method'), []));
                } catch (\Throwable $th) {
                    
                    Log::error($th);
                    $action->failure();

                    return;
                }

                $action->success();
            });
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Toggle::make('payment.enabled')
                    ->reactive(),
                Grid::make(1)
                    ->hidden(fn (Get $get) => ! $get('payment.enabled'))
                    ->schema([
                        Select::make('payment.method')
                            ->label('Payment Method')
                            ->required()
                            ->options(Payment::getAllDriverNames())
                            ->reactive(),
                        Select::make('payment.supported_currencies')
                            ->searchable()
                            ->required()
                            ->multiple()
                            ->options(Currency::query()->get()->mapWithKeys(fn (Currency $currency) => [$currency->id => $currency->name.' ('.$currency->symbol_native.')'])->toArray())
                            ->optionsLimit(250),
                        Grid::make(1)
                            ->hidden(fn (Get $get) => !$get('payment.method'))
                            ->schema(fn  (Get $get) => Payment::driver($get('payment.method'))->getSettingFormSchema()),
                    ])
            ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.payment-setting');
    }
}
