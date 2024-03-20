<?php

namespace App\Panel\Conference\Livewire\Submissions;

use App\Facades\Payment as PaymentFacade;
use App\Models\PaymentItem;
use App\Models\Submission;
use App\Panel\Conference\Livewire\Workflows\Classes\StageManager;
use App\Panel\Conference\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Squire\Models\Currency;

class Payment extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, InteractWithTenant;

    public Submission $submission;

    public array $data = [];

    public function mount()
    {
        $this->form->fill([]);
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.payment', [
            'reviewStageOpen' => StageManager::peerReview()->isStageOpen(),
        ]);
    }

    public function form(Form $form)
    {
        // return PaymentFacade::getPaymentForm($form);
        return $form
            ->statePath('data')
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('currency_id')
                            ->label('Currency')
                            ->options(
                                Currency::query()
                                    ->whereIn('id', App::getCurrentConference()->getSupportedCurrencies())
                                    ->get()
                                    ->mapWithKeys(fn (Currency $currency) => [$currency->id => $currency->name.' ('.$currency->symbol_native.')'])
                            )
                            ->required()
                            ->reactive(),
                        CheckboxList::make('items')
                            ->visible(fn (Get $get) => $get('currency_id'))
                            ->options(function (Get $get) {
                                return PaymentItem::get()
                                    ->filter(function (PaymentItem $item) use ($get): bool {
                                        foreach ($item->fees as $fee) {
                                            if (! array_key_exists('currency_id', $fee)) {
                                                continue;
                                            }
                                            if ($fee['currency_id'] === $get('currency_id')) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    })
                                    ->map(fn (PaymentItem $item): string => $item->name.': '.$item->getAmount($get('currency_id')));
                            }),
                    ]),
            ]);
    }
}
