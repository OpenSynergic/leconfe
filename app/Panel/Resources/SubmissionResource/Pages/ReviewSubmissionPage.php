<?php

namespace App\Panel\Resources\SubmissionResource\Pages;

use App\Constants\SubmissionFileCategory;
use App\Constants\SubmissionStatusRecommendation;
use App\Models\Review;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use App\Panel\Resources\SubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ReviewSubmissionPage extends Page implements HasInfolists, HasActions
{
    use InteractsWithInfolists, InteractWithTenant, InteractsWithActions;

    protected static string $resource = SubmissionResource::class;

    protected static string $view = 'panel.resources.submission-resource.pages.review-submission-page';

    public Submission $record;

    public Review $review;

    public array $reviewData = [];

    public string | null $recommendation = null;

    public function mount()
    {
        $this->review = $this->record->reviews()
            ->user(auth()->user())
            ->first();

        $this->recommendation = $this->review->recommendation;

        $this->reviewData = [
            'review_author_editor' => $this->review->review_author_editor,
            'review_editor' => $this->review->review_editor,
        ];
    }

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
                                    ->columnSpanFull()
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

    public function getHeaderActions(): array
    {
        return [
            Action::make('View Guidelines')
                ->icon("heroicon-o-information-circle")
                ->color('info')
                ->action(
                    fn () => $this->dispatch('show-guidelines')
                )
        ];
    }

    public function recommendationForm(Form $form): Form
    {
        return $form
            // ->disabled(fn (): bool => $this->review->reviewSubmitted())
            ->schema([
                Section::make()
                    ->heading("Recommendation")
                    ->schema([
                        Select::make('recommendation')
                            ->required()
                            ->label('')
                            ->searchable()
                            ->options(SubmissionStatusRecommendation::list())
                    ])
            ]);
    }

    public function reviewForm(Form $form): Form
    {
        return $form
            ->disabled(
                fn (): bool => $this->review->reviewSubmitted()
            )
            ->schema([
                Section::make()
                    ->heading("Review Form")
                    ->schema([
                        TinyEditor::make('reviewData.review_author_editor')
                            ->minHeight(300)
                            ->label("Review for Author and Editor"),
                        TinyEditor::make('reviewData.review_editor')
                            ->minHeight(300)
                            ->label("Review for Editor"),

                    ])
            ]);
    }

    private function validateAllForms()
    {
        foreach ($this->getForms() as $form) {
            $this->{$form}->validate();
        }
    }

    public function reviewAction()
    {
        return Action::make('reviewAction')
            ->requiresConfirmation()
            ->icon("lineawesome-check-circle-solid")
            ->extraAttributes(['class' => 'w-full'], true)
            ->outlined()
            ->color(
                fn (): string => !is_nulL($this->review->recommendation) ? 'gray' : 'primary'
            )
            ->label(
                fn (): string => !is_null($this->review->recommendation) ? 'Review Submitted' : 'Review'
            )
            ->disabled(
                fn (): bool => !is_null($this->review->recommendation)
            )
            ->successNotificationTitle("Review submitted successfully")
            ->action(function (Action $action) {
                $this->validateAllForms();

                // Can't submitted twice
                if ($this->review->recommendation === null) {
                    $this->review->update([
                        ...$this->reviewForm->getState()['reviewData'],
                        'recommendation' => $this->recommendation
                    ]);
                }

                $action->success();
            });
    }
}
