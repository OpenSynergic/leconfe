<?php

namespace App\Panel\Livewire\Submissions\SubmissionDetail;

use App\Models\Submission;
use App\Models\User;
use App\Panel\Livewire\Workflows\Concerns\InteractWithTenant;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Discussions extends Component implements HasForms, HasActions, HasTable
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithTable;

    public Submission $record;

    public array $chats = [];

    public array $chat = [];

    protected function paginateTableQuery(Builder $query)
    {
        return $query->simplePaginate($this->getTableRecordsPerPage() == 'all' ? $query->count() : $this->getTableRecordsPerPage());
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading("Discussion topic")
            ->query(function (): Builder {
                return User::query();
            })
            ->columns([
                TextColumn::make('given_name')
                    ->label("Topic")
                    ->color('primary')
                    ->url(fn (): string => "https://google.com")
            ]);
    }

    public function addFiles()
    {
        return Action::make('addFiles')
            ->icon("iconpark-fileadditionone")
            ->modalWidth('lg')
            ->form([
                SpatieMediaLibraryFileUpload::make("files")
                    ->statePath('chat.files')
            ])
            ->action(function (array $data) {
                dd($data, $this->chat);
            })
            ->label("Files");
    }

    public function selectDiscussion()
    {
        return ActionGroup::make([
            Action::make('discussion1')
                ->label("Discussion 1"),
            Action::make('discussion2')
                ->label("Discussion 2"),
            Action::make('discussion3')
                ->label("Discussion 3"),
        ]);
    }

    public function newDiscussion()
    {
        return Action::make('newDiscussion')
            ->icon("heroicon-o-plus-circle")
            ->modalWidth('lg')
            ->label("New Discussion")
            ->form([
                TextInput::make("name")
                    ->label("Topic"),
                Textarea::make('description')
            ]);
    }

    public function sendAction()
    {
        return Action::make('sendAction')
            ->icon("iconpark-send")
            ->label("Send");
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('chat')
                ->statePath('chat.message')
                ->rows(3)
                ->placeholder('Type your message here...')
                ->label(""),
        ]);
    }

    public function showParticipants()
    {
        return Action::make('showParticipants');
    }

    public function render()
    {
        return view('panel.livewire.submissions.submission-detail.discussions');
    }
}
