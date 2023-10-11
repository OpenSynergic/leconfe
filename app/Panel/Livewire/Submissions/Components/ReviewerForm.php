<?php

namespace App\Livewire\Submissions\Components;

use App\Models\Enums\SubmissionFileCategory;
use App\Models\Enums\SubmissionStage;
use App\Models\Enums\SubmissionStatus;
use App\Models\Enums\SubmissionStatusRecommendation;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use GuzzleHttp\BodySummarizer;
use Livewire\Component;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ReviewerForm extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public Submission $submission;

    public ReviewAssignment $reviewAssignment;

    public ?array $formReviewData = [];

    public string $recommendation;

    public function mount(Submission $submission)
    {
        $this->reviewAssignment = $this->submission->reviewAssignments()->first();
        $this->formReview->fill([
            'review-author-editor' => null,
            'review-editor' => null,
        ]);
    }

    public function getForms(): array
    {
        return [
            'formReview',
            'formDecision'
        ];
    }

    public function submit()
    {
        $reviewData = $this->formReview->getState();

        $this->formDecision->validate();

        $this->reviewAssignment([
            'recommendation' => $this->recommendation,
        ]);

        Notification::make()->success()
            ->title("Success")
            ->body("Data submitted")
            ->send();
    }

    public function formDecision(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->heading("Recommendation")
                    ->schema([
                        Select::make('recommendation')
                            ->required()
                            ->label('')
                            ->searchable()
                            ->options(SubmissionStatusRecommendation::array())
                    ])
            ]);
    }

    public function formReview(Form $form): Form
    {
        return $form
            ->statePath('formReviewData')
            ->schema([
                Section::make()
                    ->heading("Review Form")
                    ->schema([
                        RichEditor::make('review-author-editor')
                            ->label("Review for Author and Editor")
                            ->disableToolbarButtons([
                                'attachFiles'
                            ]),
                        RichEditor::make('review-editor')
                            ->label("Review for Editor")
                            ->disableToolbarButtons([
                                'attachFiles'
                            ]),
                        SpatieMediaLibraryFileUpload::make('reviewer-files')
                            ->label("Reviewer Files")
                            ->model($this->reviewAssignment)
                            ->collection(SubmissionFileCategory::ReviewerFiles->value)
                            ->multiple()
                            ->previewable(false)
                            ->downloadable()
                            ->disk('files')
                            ->preserveFilenames()
                            ->visibility('private')
                            ->saveRelationshipsUsing(static function (SpatieMediaLibraryFileUpload $component) {
                                $component->saveUploadedFiles();
                            })
                    ])
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-form');
    }
}
