<?php

namespace App\Panel\Livewire\Submissions\Components\Discussions;

use App\Models\DiscussionTopic;
use App\Models\SubmissionParticipant;
use App\Tables\Columns\ListColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use HTML5;

class DiscussionDetail extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public DiscussionTopic $topic;

    protected $listeners = [
        'refreshMessages' => '$refresh'
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->topic->discussions()->orderBy('created_at', 'desc'))
            ->heading("Discussion")
            ->description("Topic: {$this->topic->name}")
            ->actions([
                DeleteAction::make()
            ])
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('user.getFilamentAvatarUrl')
                        ->defaultImageUrl(fn ($record): string => $record->user->getFilamentAvatarUrl())
                        ->grow(false)
                        ->conversion('avatar')
                        ->width(50)
                        ->circular()
                        ->height(50),
                    Stack::make([
                        TextColumn::make('user.fullName')
                            ->description(fn ($record) => $record->created_at->format(setting('format.date')))
                            ->label("From"),
                    ]),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('message')
                            ->label("Message"),
                        ViewColumn::make('attachment-list')
                            ->view('tables.custom-views.discussions.attachment-list')
                            ->alignCenter()
                    ])
                ])
                    ->collapsible()
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.components.discussions.discussion-detail');
    }
}
