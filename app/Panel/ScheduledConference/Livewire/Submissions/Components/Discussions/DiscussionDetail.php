<?php

namespace App\Panel\ScheduledConference\Livewire\Submissions\Components\Discussions;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\DeleteAction;
use App\Facades\Setting;
use App\Models\DiscussionTopic;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class DiscussionDetail extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithTable;

    public DiscussionTopic $topic;

    protected $listeners = [
        'refreshMessages' => '$refresh',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->topic->discussions()->orderBy('created_at', 'desc'))
            ->heading(__('general.discussion'))
            ->recordActions([
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
                            ->description(fn ($record) => $record->created_at->format(Setting::get('format_date')))
                            ->label(__('general.form')),
                    ]),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('message')
                            ->label(__('general.message')),
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
        return view('panel.scheduledConference.livewire.submissions.components.discussions.discussion-detail');
    }
}
