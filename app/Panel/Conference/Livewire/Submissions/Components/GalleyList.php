<?php

namespace App\Panel\Conference\Livewire\Submissions\Components;

use App\Constants\SubmissionFileCategory;
use App\Models\Media;
use App\Models\Review;
use App\Models\Submission;
use App\Models\SubmissionGalley;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\MediaLibrary\Support\MediaStream;

class GalleyList extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.galley-list');
    }

    public function getQuery(): Builder
    {
        return $this->submission->galleys()
            ->with(['media', 'file'])
            ->orderBy('order_column')
            ->getQuery();
    }

    public function getGalleyFormSchema(): array
    {
        return [
            TextInput::make('label')
                ->label('Label')
                ->required(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->reorderable('order_column')
            ->heading('Galleys')
            ->columns([
                Split::make([
                    TextColumn::make('label')
                        ->color('primary')
                        ->url(fn (SubmissionGalley $galley) => $galley->file->media->getUrl())
                        ->openUrlInNewTab()
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Galley')
                    ->modalWidth('2xl')
                    ->icon('heroicon-o-arrow-up-tray')
            ]);
    }
}
