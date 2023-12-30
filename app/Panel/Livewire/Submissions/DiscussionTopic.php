<?php

namespace App\Panel\Livewire\Submissions;

use App\Models\Discussion;
use App\Models\Submission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class DiscussionTopic extends \Livewire\Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    public Submission $submission;

    public function mount(Submission $submission)
    {
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Discussion')
            ->query(fn () => $this->submission->discussionTopics())
            ->actions([
                Action::make('open-discussion-detail')
                    ->icon('lineawesome-eye-solid')
                    ->label("Details")
                    ->infolist(function (DiscussionTopic $discussionTopic) {
                        return [
                            TextEntry::make('test')
                                ->getStateUsing(fn (): string => "Hello")
                        ];
                    })
            ])
            ->headerActions([
                Action::make('create-topic')
                    ->icon("lineawesome-plus-solid")
                    ->label("Topic")
                    ->modalWidth("xl")
                    ->form([
                        TextInput::make('name')
                            ->label('Topic Name')
                            ->placeholder('Topic Name')
                            ->required(),
                        CheckboxList::make('user_id')
                            ->label('Participants')
                            ->default([$this->submission->user->getKey()])
                            ->options(function () {
                                return $this->submission->participants()
                                    ->get()
                                    ->mapWithKeys(function ($participant) {
                                        return [$participant->user->getKey() => $participant->user->fullName];
                                    });
                            })
                            ->descriptions(function () {
                                return $this->submission->participants()
                                    ->get()
                                    ->mapWithKeys(function ($participant) {
                                        return [$participant->user->getKey() => $participant->role->name];
                                    });
                            })
                    ])
                    ->successNotificationTitle("Topic created successfully")
                    ->action(function (array $data, Form $form) {
                        $form->validate();
                        foreach ($data['user_id'] as $user_id) {
                            $this->submission->discussionTopics()->create([
                                'name' => $data['name'],
                                'user_id' => $user_id,
                            ]);
                        }
                    })
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('open')
                    ->label("Status")
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Open' : 'Closed')
            ]);
    }

    public function render()
    {
        return view('panel.livewire.submissions.discussion-topic');
    }
}
