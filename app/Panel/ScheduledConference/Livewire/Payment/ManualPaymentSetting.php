<?php

namespace App\Panel\ScheduledConference\Livewire\Payment;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Throwable;
use App\Actions\ScheduledConferences\ScheduledConferenceUpdateAction;
use App\Forms\Components\TinyEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ManualPaymentSetting extends Component implements HasForms, HasActions
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
            ->disabled(fn () => auth()->user()->cannot('update', app()->getCurrentScheduledConference()))
            ->schema([
                Section::make()
                    ->schema([
                        Toggle::make('meta.manual_payment_enabled')
                            ->label(__('general.enabled')),
                        TextInput::make('meta.manual_payment_name')
                            ->label(__('general.name'))
                            ->placeholder(__('general.input_name_payment_method'))
                            ->required(),
                        TinyEditor::make('meta.manual_payment_instructions')
                            ->label('Instruction')
                            ->placeholder(__('general.input_payment_details'))
                            ->helperText(__('general.add_instruction_here'))
                            ->required(),
                    ]),
                Actions::make([
                    Action::make('save_changes')
                        ->label(__('general.save_changes'))
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
            ->statePath('formData');
    }

    public function render()
    {
        return view('forms.form');
    }
}
