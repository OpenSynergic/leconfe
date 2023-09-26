<?php

namespace App\Panel\Livewire\Workflows;

use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

class PeerReviewSetting extends WorkflowStage implements HasForms
{
    use InteractsWithForms;

    protected ?string $stage = 'peer-review';

    protected ?string $stageLabel = "Peer Review";

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
                ->content("The call for abstracts is not open yet, Start now or schedule opening"),
            Grid::make()
                ->schema([
                    TagsInput::make("settings.allowed_file_types")
                        ->label("Allowed File Types")
                        ->helperText("Allowed file types")
                        ->splitKeys([',', 'enter', ' ']),
                    SpatieMediaLibraryFileUpload::make('settings.paper_templates')
                        ->helperText("Upload paper templates")
                        ->label("Paper templates")
                ])
                ->columns(1)
        ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.peer-review-setting');
    }
}
