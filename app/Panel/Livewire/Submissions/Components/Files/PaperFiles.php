<?php

namespace App\Panel\Livewire\Submissions\Components\Files;

use App\Constants\SubmissionFileCategory;
use App\Mail\Templates\NewPaperUploadedMail;
use App\Models\Enums\SubmissionStage;
use App\Models\User;
use Awcodes\Shout\Components\Shout;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Mail;

class PaperFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::PAPER_FILES;

    protected string $tableHeading = "Papers";

    public function isViewOnly(): bool
    {
        if ($this->viewOnly) {
            return $this->viewOnly;
        }

        return $this->submission->stage != SubmissionStage::PeerReview;
    }

    public function uploadFormSchema(): array
    {
        return [
            Shout::make('information')
                ->content("After uploading your paper, system will send notification to the editor."),
            ...parent::uploadFormSchema()
        ];
    }
}
