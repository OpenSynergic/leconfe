<?php

namespace App\Panel\Resources\WorkflowResource\Livewire;

use App\Models\Conference;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Livewire\Component;

abstract class BaseWorkflowSetting extends Component
{
    public Conference $conference;

    public bool $isOpen = false;

    public string $stageName;

    public array $settingData = [];

    public function __construct()
    {
        $this->conference = Filament::getTenant();
    }

    public static function getDefaultScheduleForm(): array
    {
        return [
            DatePicker::make('workflow_abstract_start_date')
                ->label("Start")
                ->required()
                ->native(false)
                ->displayFormat('d/F/Y')
                ->minDate(now()->addDay())
                ->maxDate(now()->addYear()),
            DatePicker::make('workflow_abstract_end_date')
                ->label("End")
                ->required()
                ->native(false)
                ->displayFormat('d/F/Y')
                ->minDate(now()->addDay())
                ->maxDate(now()->addYear()),
        ];
    }

    public function scheduleForm(Form $form): Form
    {
        return $form->schema([
            static::getDefaultScheduleForm(),
        ]);
    }

    public function scheduleSubmit()
    {
        $this->conference->setMeta("workflow_abstract_start_date", $this->settingData['workflow_abstract_start_date']);
        $this->conference->setMeta("workflow_abstract_end_date", $this->settingData['workflow_abstract_end_date']);
    }

    public function startNow()
    {
        $this->conference->setMeta("workflow_abstract_start_date", now());
        $this->conference->setMeta("workflow_abstract_open", true);
        $this->isOpen = $this->conference->getMeta("workflow_abstract_open", true);
    }

    public function endNow()
    {
        $this->conference->setMeta("workflow_abstract_end_date", now());
        $this->conference->setMeta("workflow_abstract_open", false);
        $this->isOpen = $this->conference->getMeta("workflow_abstract_open", true);
    }
}
