<?php

namespace App\Panel\Livewire\Submissions\Components;

use App\Models\Enums\UserRole;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Models\Submission;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;

class ReviewerList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Submission $record;

    public static function renderSelectParticipant(Participant $participant): string
    {
        return view('forms.select-participant', ['participant' => $participant])->render();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $reviewerPosition = ParticipantPosition::where('name', UserRole::Reviewer->value)->first();
                return $this->record->participants()->where('participant_position_id', $reviewerPosition->getKey());
            })
            ->heading("Reviewers")
            ->headerActions([
                Action::make('add-reviewer')
                    ->label("Reviewer")
                    ->modalHeading("Assign Reviewer")
                    ->modalWidth("2xl")
                    ->form([
                        Select::make('participant_id')
                            ->label("Reviewer")
                            ->placeholder("Select a reviewer")
                            ->allowHtml()
                            ->preload()
                            ->required()
                            ->searchable()
                            ->options(function (): array {
                                return Participant::with('positions')
                                    ->whereHas('positions', function ($query) {
                                        $query->where('name', UserRole::Reviewer->value);
                                    })
                                    ->get()
                                    ->mapWithKeys(function (Participant $participant) {
                                        return [$participant->getKey() => static::renderSelectParticipant($participant)];
                                    })
                                    ->toArray();
                            }),
                        Grid::make(2)
                            ->hidden(fn (Get $get): bool => !$get('participant_id'))
                            ->schema([
                                Fieldset::make("Reviewer Statistic")
                                    ->reactive()
                                    ->schema(function (Get $get): array {
                                        $participant = Participant::find($get('participant_id'));
                                        if (!$participant) {
                                            return [];
                                        }
                                        return [
                                            Placeholder::make('Active reviews currently assigned')
                                                ->content(0),
                                            Placeholder::make('Reviews completed')
                                                ->content(0),
                                            Placeholder::make('Review requests declined')
                                                ->content(0),
                                            Placeholder::make('Average days to complete review')
                                                ->content(0),
                                        ];
                                    }),
                                Checkbox::make('send-reviewer-invitation')
                                    ->reactive()
                                    ->label("Send Reviewer Invitation")
                                    ->columnSpanFull(),
                                RichEditor::make("reviewer-invitation-message")
                                    ->visible(fn (Get $get): bool => $get('send-reviewer-invitation'))
                                    ->label("Reviewer invitation message")
                                    ->columnSpanFull(),
                            ])
                    ])
            ])
            ->columns([]);
    }
    public function render()
    {
        return view('panel.livewire.submissions.components.reviewer-list');
    }
}
