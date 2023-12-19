<?php

namespace App\Notifications;

use App\Constants\SubmissionFileCategory;
use App\Mail\Templates\NewPaperUploadedMail;
use App\Mail\Templates\NewRevisionUploadedMail;
use App\Models\SubmissionFile;
use App\Panel\Resources\SubmissionResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubmissionFileUploaded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SubmissionFile $submissionFile)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Currently, only the new paper and revision file will send a notification to the editor.
     */
    public function toMail(object $notifiable)
    {
        $mailTempalte = match ($this->submissionFile->category) {
            SubmissionFileCategory::PAPER_FILES => NewPaperUploadedMail::class,
            SubmissionFileCategory::REVISION_FILES => NewRevisionUploadedMail::class,
            default => null
        };

        if (! $mailTempalte) {
            return null;
        }

        return (new $mailTempalte($this->submissionFile))
            ->to($notifiable);
    }

    public function toDatabase(object $notifiable)
    {
        return FilamentNotification::make()
            ->icon('lineawesome-exclamation-circle-solid')
            ->iconColor('primary')
            ->title(function () {
                return match ($this->submissionFile->category) {
                    SubmissionFileCategory::PAPER_FILES => 'New Paper Uploaded',
                    SubmissionFileCategory::REVISION_FILES => 'New Revision Uploaded',
                    default => 'New File Uploaded'
                };
            })
            ->body("Title: {$this->submissionFile->submission->getMeta('title')}")
            ->actions([
                Action::make('new-submission')
                    ->url(SubmissionResource::getUrl('view', ['record' => $this->submissionFile->submission, 'tenant' => $this->submissionFile->submission->conference]))
                    ->label('View')
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
