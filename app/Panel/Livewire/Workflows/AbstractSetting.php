<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Submissions\Components\Files\SubmissionFilesTable;
use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

/**
 * Create a job to change status
 */
class AbstractSetting extends WorkflowStage implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected ?string $stage = 'call-for-abstract';

    protected ?string $stageLabel = 'Call for Abstract';

    public function mount()
    {
        $this->form->fill([
            'settings' => [
                'allowed_file_types' => $this->getSetting('allowed_file_types', SubmissionFilesTable::ACCEPTED_FILE_TYPES),
            ],
        ]);
    }

    public function submitAction()
    {
        return Action::make('submitAction')
            ->label('Save')
            ->icon('lineawesome-save-solid')
            ->successNotificationTitle('Saved')
            ->action(function (Action $action) {
                $this->form->validate();
                foreach ($this->form->getState()['settings'] as $key => $value) {
                    $this->updateSetting($key, $value);
                }
                $action->success();
            });
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Shout::make('settings.stage-closed')
                ->hidden(fn (): bool => $this->isStageOpen())
                ->color('warning')
                ->content("The {$this->getStageLabel()} is not open yet, Start now or schedule opening"),
            Grid::make()->schema([
                TagsInput::make('settings.allowed_file_types')
                    ->label('Allowed File Types')
                    ->helperText('Allowed file types for abstracts')
                    ->splitKeys([',', 'enter', ' ']),
            ])
                ->columns(1),
        ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.abstract-setting');
    }
}
