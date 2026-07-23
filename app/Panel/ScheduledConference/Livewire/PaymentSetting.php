<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\ScheduledConferences\ScheduledConferenceUpdateAction;
use App\Models\ScheduledConference;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class PaymentSetting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([
            'meta' => app()->getCurrentScheduledConference()->getAllMeta(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('general.submission_payment'))
                    ->schema([
                        Checkbox::make('meta.submission_payment')
                            ->live()
                            ->label(__('general.enable_submission_payment')),
                        Radio::make('meta.submission_payment_auto_notify')
                            ->label(__('general.invoice_mode'))
                            ->options([
                                '1' => __('general.auto_send_invoice'),
                                '0' => __('general.manual'),
                            ])
                            ->descriptions([
                                '1' => __('general.auto_send_invoice_help'),
                                '0' => __('general.manual_invoice_help'),
                            ])
                            ->afterStateHydrated(function (Radio $component, $state): void {
                                $component->state(($state === false || $state === '0') ? '0' : '1');
                            })
                            ->live()
                            ->visible(fn (Get $get) => (bool) $get('meta.submission_payment')),
                        Select::make('meta.submission_billing_stage')
                            ->label(__('general.when_submission_payment_available'))
                            ->options(ScheduledConference::getSubmissionBillingStageOptions())
                            ->visible(fn (Get $get) => (bool) $get('meta.submission_payment') && $get('meta.submission_payment_auto_notify') !== '0'),
                    ]),
                Section::make(__('general.participant_payment'))
                    ->schema([
                        Checkbox::make('meta.participant_payment')
                            ->live()
                            ->label(__('general.enable_participant_payment')),
                        Radio::make('meta.participant_payment_auto_notify')
                            ->label(__('general.invoice_mode'))
                            ->options([
                                '1' => __('general.auto_send_invoice'),
                                '0' => __('general.manual'),
                            ])
                            ->descriptions([
                                '1' => __('general.participant_auto_send_invoice_help'),
                                '0' => __('general.manual_invoice_help'),
                            ])
                            ->afterStateHydrated(function (Radio $component, $state): void {
                                $component->state(($state === false || $state === '0') ? '0' : '1');
                            })
                            ->live()
                            ->visible(fn (Get $get) => (bool) $get('meta.participant_payment')),
                    ]),
                Section::make(__('general.payment_period'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('meta.payment_opened_at')
                                    ->label(__('general.payment_start_date'))
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-calendar-days'),
                                DatePicker::make('meta.payment_closed_at')
                                    ->label(__('general.payment_end_date'))
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-calendar-days')
                                    ->after('meta.payment_opened_at'),
                            ]),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->failureNotificationTitle(__('general.data_could_not_saved'))
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();

                            try {
                                ScheduledConferenceUpdateAction::run(app()->getCurrentScheduledConference(), $formData);
                            } catch (Throwable $th) {
                                $action->failure();
                                throw $th;
                            }

                            $action->success();
                        }),
                ]),
            ])
            ->disabled(fn () => ! auth()->user()->can('update', app()->getCurrentScheduledConference()))
            ->statePath('formData');
    }

    public function render()
    {
        return view('forms.form');
    }
}
