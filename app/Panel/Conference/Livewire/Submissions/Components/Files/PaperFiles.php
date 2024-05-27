<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Files;

use App\Actions\SubmissionFiles\UploadSubmissionFileAction;
use App\Constants\SubmissionFileCategory;
use App\Mail\Templates\NewPaperUploadedMail;
use App\Models\SubmissionFileType;
use App\Models\User;
use Awcodes\Shout\Components\Shout;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\Mail;

class PaperFiles extends SubmissionFilesTable
{
    protected ?string $category = SubmissionFileCategory::PAPER_FILES;

    protected string $tableHeading = 'Papers';

    public function isViewOnly(): bool
    {
        return ! auth()->user()->can('uploadPaper', $this->submission);
    }

    public function uploadFormSchema(): array
    {
        return [
            Shout::make('information')
                ->content('After uploading your paper, system will send notification to the editor.'),
            ...parent::uploadFormSchema(),
        ];
    }

    public function handleUploadAction(array $data, TableAction $action)
    {
        $files = $this->submission->getMedia($this->category);

        $submissionFiles = [];

        foreach ($files as $file) {
            $submissionFiles[] = UploadSubmissionFileAction::run(
                $this->submission,
                $file,
                $this->category,
                SubmissionFileType::find($data['type'])
            );
        }

        $submissionFile = end($submissionFiles) ?? null;

        if ($submissionFile) {
            $editors = User::editorSubmission($this->submission)->get();

            $emails = $editors->pluck('email');
            Mail::send(new NewPaperUploadedMail($submissionFile), [], function ($message) use ($emails) {
                $message->to($emails);
            });
        }

        $action->success();
    }
}
