<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Models\Enums\SubmissionFileCategory;
use App\Models\Enums\SubmissionStatusRecommendation;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ReviewSubmissionPage extends Page implements HasInfolists
{
    use InteractsWithInfolists, InteractWithTenant;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.review-submission-page';

    public Submission $record;

    public function getHeading(): string|Htmlable
    {
        return "Review: " . $this->record->getMeta('title');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                InfolistSection::make()
                    ->heading("Submission Details")
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('Title')
                                    ->color('gray')
                                    ->getStateUsing(
                                        fn (Submission $record): string => $record->getMeta('title')
                                    ),
                                TextEntry::make('Keywords')
                                    ->color('gray')
                                    ->getStateUsing(
                                        fn (Submission $record): string => $record->tagsWithType('submissionKeywords')->pluck('name')->join(', ')
                                    ),
                                TextEntry::make('Abstract')
                                    ->color('gray')
                                    ->html()
                                    ->getStateUsing(
                                        fn (Submission $record): string => $record->getMeta('abstract')
                                    ),
                            ])
                    ])
            ]);
    }

    public function getForms(): array
    {
        return [
            'reviewForm',
            'recommendationForm'
        ];
    }

    public function recommendationForm(Form $form): Form
    {
        return $form->schema([
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

    public function reviewForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->heading("Review Form")
                    ->schema([
                        RichEditor::make('review-author-editor')
                            ->label("Review for Author and Editor")
                            ->disableToolbarButtons([
                                'attachFiles'
                            ]),
                        RichEditor::make('review-zxc-editor')
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
                            ->model(ReviewAssignment::class)
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

    private function validateAllForms()
    {
        foreach ($this->getForms() as $form) {
            $this->{$form}->validate();
        }
    }

    public function submit(): void
    {
        $this->validateAllForms();
    }
}
