<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;
use App\Panel\Livewire\Workflows\Classes\StageManager;

class AbstractFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::ABSTRACT_FILES;

    protected string $tableHeading = "Abstract Files";

    protected $listeners = [
        'refreshAbstractFiles' => '$refresh'
    ];

    public function getAcceptedFiles(): array
    {
        return StageManager::callForAbstract()
            ->getSetting(
                'allowed_file_types',
                parent::ACCEPTED_FILE_TYPES
            );
    }


    public function isViewOnly(): bool
    {
        if ($this->viewOnly) {
            return $this->viewOnly;
        }

        // Jika sudah submit, maka tidak akan bisa lagi melakukan pengeditan
        if ($this->submission->stage == SubmissionStage::CallforAbstract) {
            return true;
        }

        return $this->submission->stage != SubmissionStage::Wizard;
    }
}