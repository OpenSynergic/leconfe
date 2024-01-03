<?php

namespace App\Panel\Livewire\Submissions\Components\Discussions;

use App\Models\Discussion;
use App\Models\DiscussionTopic;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class DiscussionDetailForm extends \Livewire\Component implements HasForms
{
    use InteractsWithForms;

    public DiscussionTopic $topic;

    public string $message;

    public array $attachments = [];

    public function mount()
    {
        $this->form->fill([
            'message' => '',
            'attachments' => [],
        ]);
    }

    public function submit()
    {
        $this->form->validate();

        if (!$this->topic->open) {
            return abort('403', 'Discussion is closed.');
        }

        $formData = $this->form->getState();

        $discussion = $this->topic->discussions()->create($formData);

        if (isset($formData['attachments'])) {
            foreach ($formData['attachments'] as $media) {
                $discussion->addMedia($media->getRealPath())
                    ->usingName($media->getClientOriginalName())
                    ->toMediaCollection('discussion-attachment');
            }
        }

        Notification::make('discussion-added')
            ->success()
            ->title("Discussion Added")
            ->body("Discussion has been added successfully.")
            ->send();

        $this->form->fill([
            'message' => '',
            'attachments' => [],
        ]); // Reset Form Input

        $this->dispatch('refreshMessages');
    }

    public function form(Form $form)
    {
        return $form
            ->disabled(fn (): bool => !$this->topic->open)
            ->schema([
                Shout::make('discussion-alert')
                    ->type('warning')
                    ->hidden(fn (): bool => $this->topic->open)
                    ->content('Can not add message to closed discussion.'),
                Textarea::make('message')
                    ->placeholder('Message')
                    ->columnSpanFull()
                    ->required()
                    ->rows(5),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection('discussion-attachment')
                    ->disk('private-files')
                    ->dehydrated()
                    ->preserveFilenames()
                    ->multiple()
                    ->previewable(false)
                    ->downloadable()
                    ->visibility('private')
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.discussions.discussion-detail-form');
    }
}
