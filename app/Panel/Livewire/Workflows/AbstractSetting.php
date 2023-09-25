<?php

namespace App\Panel\Livewire\Workflows;

use App\Models\Conference;
use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use App\Panel\Livewire\Workflows\Traits\CanOpenStage;
use App\Panel\Pages\Settings\Workflow;
use Awcodes\Shout\Components\Shout;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

/**
 * Create a job to change status
 */
class AbstractSetting extends WorkflowStage implements HasForms
{
    use InteractsWithForms;

    protected ?string $stage = 'call-for-abstract';

    protected ?string $stageLabel = 'Call for Abstract';

    public function mount()
    {
        $this->form->fill([
            'settings' => [
                'allowed_file_types' => $this->getSetting('allowed_file_types', ['pdf', 'docx', 'doc'])
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Shout::make('stage-closed')
                ->hidden(fn (): bool => $this->isStageOpen())
                ->color('warning')
                ->content("The {$this->getStageLabel()} is not open yet, Start now or schedule opening"),
            Grid::make()->schema([
                TagsInput::make("settings.allowed_file_types")
                    ->label("Allowed File Types")
                    ->helperText("Allowed file types for abstracts")
                    ->splitKeys([',', 'enter', ' '])
            ])
                ->columns(1)
                ->hidden(fn (): bool => !$this->isStageOpen()),
        ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.abstract-setting');
    }
}
