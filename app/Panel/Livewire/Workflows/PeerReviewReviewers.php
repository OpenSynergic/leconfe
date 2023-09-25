<?php

namespace App\Panel\Livewire\Workflows;

use App\Actions\Participants\ParticipantCreateAction;
use App\Actions\User\UserCreateAction;
use App\Models\Enums\UserRole;
use App\Models\Meta;
use App\Models\Participant;
use App\Models\ParticipantPosition;
use App\Panel\Livewire\Workflows\Base\WorkflowStage;
use App\Panel\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PeerReviewReviewers extends WorkflowStage implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function form(Form $form): Form
    {
        return $form;
    }

    public function getQuery(): Builder
    {
        return Participant::query()
            ->orderBy('order_column')
            ->with([
                'positions' => fn ($query) => $query
                    ->where('type', 'reviewer'),
                'media',
                'meta'
            ])
            ->whereHas(
                'positions',
                fn (Builder $query) => $query->where('type', 'reviewer')
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading("No reviewers yet")
            ->query(fn (): Builder => $this->getQuery())
            ->heading("Reviewers")
            ->columns([
                ...ParticipantResource::generalTableColumns(),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make("add_existing")
                        ->form([]),
                    Action::make("add_new")
                        ->form([
                            Grid::make(2)
                                ->schema([
                                    ...ParticipantResource::generalFormField(),
                                    TextInput::make('password')
                                        ->columnSpanFull()
                                        ->dehydrateStateUsing(function ($state) {
                                            return Hash::make($state);
                                        })
                                        ->required()
                                        ->password(),
                                    // Select::make('positions')
                                    //     ->label('Position')
                                    //     ->required()
                                    //     ->searchable()
                                    //     ->relationship(
                                    //         name: 'positions',
                                    //         titleAttribute: 'name',
                                    //         modifyQueryUsing: fn (Builder $query) => $query->where('type', 'reviewer'),
                                    //     )
                                    //     ->default(fn () => ParticipantPosition::where('type', 'reviewer')->first()->id)
                                    //     ->columnSpanFull()
                                    //     ->preload()
                                    //     ->saveRelationshipsUsing(function (Select $component, Model $record, $state) {
                                    //         $record->positions()->detach($record->positions);
                                    //         $record->positions()->attach($state);
                                    //     }),
                                    ...ParticipantResource::additionalFormField(),
                                ])
                        ])
                        ->successNotificationTitle("Reviewer created")
                        ->action(function (array $data, Action $action) {
                            $participant = ParticipantCreateAction::run($data);
                            $participant->positions()->attach(
                                ParticipantPosition::where('type', 'reviewer')->first()?->id
                            );
                            $participant->createUserAccount(UserRole::Reviewer, $data['password']);
                            $action->success();
                        })
                ])
                    ->button(),
            ]);
    }

    public function render()
    {
        return view('panel.livewire.workflows.peer-review-reviewers');
    }
}
