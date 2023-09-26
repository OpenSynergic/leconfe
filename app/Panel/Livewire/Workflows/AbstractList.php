<?php

namespace App\Panel\Livewire\Workflows;

use App\Models\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use App\Panel\Resources\SubmissionResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class AbstractList extends WorkflowStage implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected ?string $stage = 'call-for-abstract';

    protected ?string $stageLabel = 'Call for Abstract';

    public function getQuery(): Builder
    {
        return Submission::with(['meta'])->whereStatus(SubmissionStatus::New);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("List of Abstracts")
            ->query(fn (): Builder => $this->getQuery())
            ->columns([
                TextColumn::make("title")
                    ->getStateUsing(fn (Submission $record): string => $record->getMeta('title'))
                    ->url(fn (Submission $record): string => SubmissionResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab()
                    ->color("primary")
                    ->searchable(),
                TextColumn::make('status')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color("danger"),
                ])
            ])
            ->headerActions([
                Action::make("submit_abstract")
                    ->label(function (): string {
                        return $this->isStageOpen() ? "Submit Abstract" : "Submission Closed";
                    })
                    ->color(fn () => !$this->isStageOpen() ? "warning" : "primary")
                    ->disabled(fn () => !$this->isStageOpen())
                    ->url(SubmissionResource::getUrl('create'))
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('accept')
                        ->icon('heroicon-o-check-circle')
                        ->color("success"),
                    Action::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color("danger"),
                ])
            ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.abstract-list');
    }
}
