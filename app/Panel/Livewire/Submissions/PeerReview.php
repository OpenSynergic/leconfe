<?php

namespace App\Panel\Livewire\Submissions;

use App\Infolists\Components\LivewireEntry;
use App\Models\Submission;
use App\Panel\Livewire\Submissions\SubmissionDetail\Discussions;
use App\Panel\Livewire\Tables\Submissions\SubmissionFilesTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PeerReview extends Component implements HasForms, HasActions, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public Submission $submission;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->submission->papers())
            ->heading("Papers")
            ->headerActions([
                Action::make('download-template')
                    ->label("Download Template")
                    ->color("gray"),
                Action::make('upload-paper')
                    ->modalWidth("xl")
                    ->form([
                        SpatieMediaLibraryFileUpload::make('submission-papers')
                    ])
            ])
            ->columns([]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.peer-review');
    }
}
