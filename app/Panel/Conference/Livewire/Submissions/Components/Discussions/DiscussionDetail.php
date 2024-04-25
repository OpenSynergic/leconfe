<?php

namespace App\Panel\Conference\Livewire\Submissions\Components\Discussions;

use App\Facades\Settings;
use App\Models\DiscussionTopic;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

class DiscussionDetail extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public DiscussionTopic $topic;

    protected $listeners = [
        'refreshMessages' => '$refresh',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->topic->discussions()->orderBy('created_at', 'desc'))
            ->heading('Discussion')
            ->actions([
                DeleteAction::make()
                    ->authorize('Discussion:delete')
                    ->visible(fn (): bool => $this->topic->open),
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
                            ->description(fn ($record) => $record->created_at->format(Settings::get('format.date')))
                            ->label('From'),
                    ]),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('message')
                            ->label('Message'),
                        ViewColumn::make('attachment-list')
                            ->view('tables.custom-views.discussions.attachment-list')
                            ->alignCenter(),
                    ]),
                ])
                    ->collapsed(false)
                    ->collapsible(),
            ]);
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.discussions.discussion-detail');
    }
}
