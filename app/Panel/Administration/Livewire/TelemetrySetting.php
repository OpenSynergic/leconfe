<?php

namespace App\Panel\Administration\Livewire;

use App\Services\Telemetry\TelemetrySettings;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class TelemetrySetting extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $formData = [];

    public bool $showUpgradeNotice = false;

    public function mount(): void
    {
        $telemetrySettings = app(TelemetrySettings::class);
        $this->showUpgradeNotice = ! $telemetrySettings->upgradeNoticeShown()
            && $telemetrySettings->upgradeNoticePending();

        if ($this->showUpgradeNotice) {
            $telemetrySettings->markUpgradeNoticeShown();
        }

        $this->form->fill([
            'telemetry_enabled' => $telemetrySettings->enabled(),
        ]);
    }

    public function render()
    {
        return view('forms.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('general.installation_usage_telemetry'))
                    ->description(__('general.telemetry_setting_description'))
                    ->schema([
                        Placeholder::make('upgrade_notice')
                            ->label(__('general.telemetry_upgrade_heading'))
                            ->content(fn (): string => implode(' ', [
                                __('general.telemetry_upgrade_notice'),
                                __('general.telemetry_upgrade_data'),
                                __('general.telemetry_upgrade_settings'),
                            ]))
                            ->visible(fn (): bool => $this->showUpgradeNotice),
                        Checkbox::make('telemetry_enabled')
                            ->label(__('general.telemetry_send_label'))
                            ->helperText(__('general.telemetry_opt_out_helper')),
                        Placeholder::make('sent_data')
                            ->label(__('general.telemetry_data_sent'))
                            ->content(new HtmlString('<ul class="list-disc pl-5 space-y-1"><li>'.e(__('general.telemetry_installation_data')).'</li><li>'.e(__('general.telemetry_environment_data')).'</li><li>'.e(__('general.telemetry_aggregate_data')).'</li><li>'.e(__('general.telemetry_prohibited_data')).'</li></ul>')),
                        Placeholder::make('last_status')
                            ->label(__('general.telemetry_last_send'))
                            ->content(fn (): string => $this->lastSendDescription()),
                    ])
                    ->columns(1),
                Actions::make([
                    Action::make('save')
                        ->label(__('general.save'))
                        ->successNotificationTitle(__('general.saved'))
                        ->action(function (Action $action): void {
                            $data = $this->form->getState();
                            app(TelemetrySettings::class)->setEnabled((bool) ($data['telemetry_enabled'] ?? true));
                            $action->sendSuccessNotification();
                        }),
                ]),
            ])
            ->statePath('formData');
    }

    private function lastSendDescription(): string
    {
        $status = app(TelemetrySettings::class)->status();

        if (! $status['status']) {
            return __('general.telemetry_not_sent');
        }

        $description = ucfirst((string) $status['status']);
        if ($status['attempted_at']) {
            $description = __('general.telemetry_status_at', [
                'status' => $description,
                'time' => $status['attempted_at'],
            ]);
        }

        if ($status['error']) {
            $description .= ' — '.$status['error'];
        }

        return $description;
    }
}
