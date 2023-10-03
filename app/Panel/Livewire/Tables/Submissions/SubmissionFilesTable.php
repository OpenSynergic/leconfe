<?php

namespace App\Panel\Livewire\Tables\Submissions;

use App\Models\Media;
use App\Models\Submission;
use App\Schemas\SubmissionFileSchema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\MediaLibrary\Support\MediaStream;

class SubmissionFilesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $record;

    public bool $viewOnly = false;

    protected $listeners = ['refreshLivewire' => '$refresh'];

    public function render()
    {
        return view('panel.livewire.tables.submissions.submission-files');
    }

    protected function getTableQuery(): Builder
    {
        return $this->record->files()->getQuery();
    }

    protected function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading("Files")
            ->columns([
                ...SubmissionFileSchema::defaultTableColumns()
            ])
            ->headerActions([])
            ->actions([
                DeleteAction::make()->hidden($this->viewOnly),
            ]);
    }
}
