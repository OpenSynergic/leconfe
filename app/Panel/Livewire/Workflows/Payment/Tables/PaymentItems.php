<?php

namespace App\Panel\Livewire\Workflows\Payment\Tables;

use App\Models\SubmissionPaymentItem;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Stevebauman\Purify\Facades\Purify;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Tables\Columns\IndexColumn;
use App\Tables\Columns\ListColumn;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Squire\Models\Currency;

class PaymentItems extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        return view('panel.livewire.workflows.payment.tables.payment-items');
    }

    public function table(Table $table): Table
    {
        $formField = [
            TextInput::make('name')
                ->required(),
            Textarea::make('description')
                ->autosize(),
            Repeater::make('fees')
                ->required()
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('currency_id')
                                ->label('Currency')
                                ->searchable()
                                ->required()
                                ->disabled()
                                ->dehydrated(true)
                                // ->options(Currency::whereIn('id', App::getCurrentConference()->getMeta('workflow.payment.supported_currencies') ?? [])->pluck('name', 'id'))
                                ->options(Currency::pluck('name', 'id'))
                                ->optionsLimit(250)
                                ->distinct(),
                            TextInput::make('fee')
                                ->required()
                                ->minValue(1)
                                ->prefix(fn(Get $get) => $get('currency_id') ? Currency::find($get('currency_id'))->symbol_native : null)
                                ->numeric(),
                        ]),
                ])
                ->deletable(false)
                ->reorderable(false)
                // ->reorderableWithButtons()
                ->addable(false)
                ->addActionLabel('Add Fee')
        ];

        return $table
            ->query(SubmissionPaymentItem::query())
            ->reorderable('order_column')
            ->heading('Payment Items')
            ->paginated(false)
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label("New Payment Item")
                    ->mountUsing(function (Form $form) {
                        $fees = collect(App::getCurrentConference()->getMeta('workflow.payment.supported_currencies'))->map(function ($currency) {
                            return [
                                'currency_id' => $currency,
                                'fee' => 0,
                            ];
                        })->toArray();
                        $form->fill([
                            'fees' => $fees
                        ]);
                    })
                    ->model(SubmissionPaymentItem::class)
                    ->modalWidth('2xl')
                    ->form($formField)
            ])
            ->filters([
                // ...
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function ($data) {
                        $supportedCurrencies = App::getCurrentConference()->getMeta('workflow.payment.supported_currencies') ?? [];
                        $fees = collect($supportedCurrencies)
                            ->map(fn ($currency) => ['currency_id' => $currency, 'fee' => 0])
                            ->keyBy('currency_id')
                            ->merge(collect($data['fees'])->keyBy('currency_id'))
                            ->filter(fn($fee) => in_array($fee['currency_id'], $supportedCurrencies))
                            ->toArray();
                        $data['fees'] = $fees;

                        return $data;
                    })
                    ->modalWidth('2xl')
                    ->form($formField),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
