<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class EditingSetting extends WorkflowStage implements HasForms
{
    use InteractsWithForms;

    protected ?string $stage = 'editing';

    protected ?string $stageLabel = 'Editing';

    public array $settings = [];

    public function mount()
    {
        $this->form->fill([
            'settings' => [
                'production_allowed_file_types' => $this->getSetting('production_allowed_file_types', ['pdf']),
                'draft_allowed_file_types' => $this->getSetting('draft_allowed_file_types', ['pdf', 'doc', 'docx'])
            ]
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Shout::make('stage-closed')
                    ->hidden(fn (): bool => $this->isStageOpen())
                    ->color('warning')
                    ->content("The {$this->getStageLabel()} is not open yet, Start now or schedule opening"),
                Grid::make()
                    ->schema([
                        TagsInput::make('settings.draft_allowed_file_types')
                            ->label('Draft File Type')
                            ->helperText('Allowed file types for draft files')
                            ->splitKeys([',']),
                        TagsInput::make('settings.production_allowed_file_types')
                            ->label('Production File Type')
                            ->helperText('Allowed file types for production files')
                            ->splitKeys([',']),
                    ])
                    ->columns(1),
            ]);
    }

    public function save()
    {
        $data = $this->form->getState();
        foreach ($data['settings'] as $settingName => $settingValue) {
            $this->updateSetting($settingName, $settingValue);
        }

        Notification::make('editing-saved')
            ->title('Saved')
            ->body('Settings Saved successfully')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.livewire.workflows.editing-setting');
    }
}
