<?php

namespace App\Panel\Conference\Livewire\Submissions\Components;

use App\Actions\Presenters\PresenterCreateAction;
use App\Actions\Presenters\PresenterDeleteAction;
use App\Actions\Presenters\PresenterUpdateAction;
use App\Mail\Templates\ApprovedPresenterMail;
use App\Models\Enums\PresenterStatus;
use App\Models\Enums\UserRole;
use App\Models\MailTemplate;
use App\Models\Presenter;
use App\Models\Submission;
use App\Models\Timeline;
use App\Panel\Conference\Livewire\Forms\Conferences\ContributorForm;
use App\Panel\Conference\Resources\Conferences\ParticipantResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;

class PresenterList extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Submission $submission;

    public bool $viewOnly = false;

    public function getQuery(): Builder
    {
        return Presenter::query()
            ->whereSubmissionId($this->submission->getKey())
            ->with(['media', 'meta'])
            ->orderBy('order_column');
    }

    public function getPresenterFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    ...ContributorForm::generalFormField($this->submission),
                    ...ContributorForm::additionalFormField($this->submission),
                ])
                ->columnSpan([
                    'lg' => 2,
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable(
                fn () => $this->viewOnly ? false : 'order_column'
            )
            ->heading('Presenters')
            ->query(
                fn (): Builder => $this->getQuery()
            )
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('3xl')
                        ->hidden(
                            fn (Model $record): bool => $record->email == auth()->user()->email
                        )
                        ->mutateRecordDataUsing(function (array $data, Model $record) {
                            $data['meta'] = $record->getAllMeta();
                            return $data;
                        })
                        ->using(fn (array $data, Presenter $record) => PresenterUpdateAction::run($record, $data))
                        ->form($this->getPresenterFormSchema()),
                    DeleteAction::make()
                        ->using(fn (array $data, Model $record) => PresenterDeleteAction::run($record))
                        ->hidden(
                            fn (Model $record): bool => $record->email == auth()->user()->email
                        ),
                ])
                    ->hidden($this->viewOnly),
            ])
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make()
                        ->label('Create new')
                        ->modalWidth('2xl')
                        ->modalHeading('Add Presenter')
                        ->successNotificationTitle('Presenter added')
                        ->record($this->submission)
                        ->form($this->getPresenterFormSchema())
                        ->using(function (array $data, Action $action) {
                            $presenter = Presenter::whereSubmissionId($this->submission->getKey())->email($data['email'])->first();
                            $data['status'] = auth()->user()->hasRole([UserRole::Admin->value]) ? PresenterStatus::Approve : PresenterStatus::Unchecked;
                            if (! $presenter) {
                                $presenter = PresenterCreateAction::run($this->submission, $data);

                                if ($presenter->status == PresenterStatus::Approve) {
                                    $mailTemplate = MailTemplate::where('mailable', ApprovedPresenterMail::class)->first();
                                    $getTemplateMail = (new ApprovedPresenterMail($presenter))
                                        ->subjectUsing($mailTemplate->subject)
                                        ->contentUsing($mailTemplate->getHtmlTemplate());
    
                                    $presenter->setManyMeta([
                                        'notes' => $getTemplateMail->message,
                                        'approved_by' => auth()->user()->id,
                                        'approved_at' => now()->toDateTimeString(),
                                    ]);
    
                                    try {
                                        Mail::to($presenter->email)
                                            ->send($getTemplateMail);
                                    } catch (\Exception $e) {
                                        $action->failureNotificationTitle('The email notification was not delivered.');
                                        $action->failure();
                                    }
                                }
                            }
                            
                            return $presenter;
                        }),
                    Action::make('add_existing')
                        ->label('Add from existing')
                        ->modalWidth('lg')
                        ->form([
                            Grid::make()
                                ->schema([
                                    Select::make('presenter_id')
                                        ->label('Name')
                                        ->options(function () {
                                            $presenters = $this->getQuery()->pluck('email')->toArray();

                                            return Presenter::query()
                                                ->limit(10)
                                                ->whereNotIn('email', $presenters)
                                                ->get()
                                                ->unique('email')
                                                ->mapWithKeys(fn (Presenter $presenter) => [$presenter->getKey() => static::renderSelectPresenter($presenter)])
                                                ->toArray();
                                        })
                                        ->getSearchResultsUsing(
                                            function (string $search) {
                                                $presenters = $this->getQuery()->pluck('email')->toArray();
        
                                                return Presenter::query()
                                                    ->with(['media', 'meta'])
                                                    ->whereNotIn('email', $presenters)
                                                    ->where(fn ($query) => $query->where('given_name', 'LIKE', "%{$search}%")
                                                        ->orWhere('family_name', 'LIKE', "%{$search}%")
                                                        ->orWhere('email', 'LIKE', "%{$search}%"))
                                                    ->get()
                                                    ->mapWithKeys(fn (Presenter $presenter) => [$presenter->getKey() => static::renderSelectPresenter($presenter)])
                                                    ->toArray();
                                            }
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->allowHtml()
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->action(function (array $data, Action $action) {
                            $presenter = Presenter::find($data['presenter_id']);

                            $newPresenter = $this->submission->presenters()->create([
                                ...$presenter->only(['given_name', 'family_name', 'email']),
                                'status' => auth()->user()->hasRole([UserRole::Admin->value]) ? PresenterStatus::Approve : PresenterStatus::Unchecked,
                            ]);

                            if ($newPresenter->status == PresenterStatus::Approve) {
                                $mailTemplate = MailTemplate::where('mailable', ApprovedPresenterMail::class)->first();
                                $getTemplateMail = (new ApprovedPresenterMail($newPresenter))
                                    ->subjectUsing($mailTemplate->subject)
                                    ->contentUsing($mailTemplate->getHtmlTemplate());

                                $newPresenter->setManyMeta([
                                    'notes' => $getTemplateMail->message,
                                    'approved_by' => auth()->user()->id,
                                    'approved_at' => now()->toDateTimeString(),
                                ]);

                                try {
                                    Mail::to($newPresenter->email)
                                        ->send($getTemplateMail);
                                } catch (\Exception $e) {
                                    $action->failureNotificationTitle('The email notification was not delivered.');
                                    $action->failure();
                                }
                            }

                            if ($meta = $presenter->getAllMeta()->toArray()) {
                                $newPresenter->setManyMeta($meta);
                            }

                            $action->success();
                        }),
                ])
                    ->button()
                    ->label('Add Presenter')
                    ->hidden($this->viewOnly),
            ])
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('profile')
                        ->grow(false)
                        ->collection('profile')
                        ->conversion('avatar')
                        ->width(50)
                        ->height(50)
                        ->defaultImageUrl(
                            fn (Model $record): string => $record->getFilamentAvatarUrl()
                        )
                        ->extraCellAttributes([
                            'style' => 'width: 1px',
                        ])
                        ->circular()
                        ->toggleable(! $this->viewOnly),
                    Stack::make([
                        TextColumn::make('fullName')
                            ->formatStateUsing(function (Model $record) {
                                if ($record->email == auth()->user()->email) {
                                    return $record->fullName.' (You)';
                                }

                                return $record->fullName;
                            }),
                        TextColumn::make('affiliation')
                            ->size('xs')
                            ->getStateUsing(
                                fn (Model $record) => $record->getMeta('affiliation')
                            )
                            ->icon('heroicon-o-building-library')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('email')
                            ->size('xs')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])->space(1),
                    TextColumn::make('role.name')
                        ->badge()
                        ->alignEnd(),
                ]),
            ]);
    }

    public static function renderSelectPresenter(Presenter $presenter): string
    {
        return view('forms.select-participant', ['participant' => $presenter])->render();
    }

    public function render()
    {
        return view('panel.conference.livewire.submissions.components.presenter-list');
    }
}
