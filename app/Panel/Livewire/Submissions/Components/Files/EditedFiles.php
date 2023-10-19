<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use Filament\Tables\Table;

class EditedFiles extends SubmissionFilesTable
{
    public string $category = SubmissionFileCategory::EDITED_FILES;

    public function tableHeading(): string
    {
        return "Edited Files";
    }
}
