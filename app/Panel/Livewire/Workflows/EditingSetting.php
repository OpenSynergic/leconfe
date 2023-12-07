<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class EditingSetting extends WorkflowStage implements HasForms
{
    use InteractsWithForms;

    protected ?string $stage = 'editing';

    protected ?string $stageLabel = 'Editing';

    public function mount()
    {
        $this->form->fill([
            'settings' => [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Shout::make('stage-closed')
                ->hidden(fn (): bool => $this->isStageOpen())
                ->color('warning')
                ->content("The {$this->getStageLabel()} is not open yet, Start now or schedule opening"),
            Grid::make()
                ->schema([])
                ->columns(1),
        ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.editing-setting');
    }
}
