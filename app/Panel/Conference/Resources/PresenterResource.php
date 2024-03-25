<?php

namespace App\Panel\Conference\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Presenter;
use Filament\Tables\Table;
use Squire\Models\Country;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use App\Models\Enums\PresenterStatus;
use App\Models\Timeline;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Panel\Conference\Resources\PresenterResource\Pages;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Mail;

class PresenterResource extends Resource
{
    protected static ?string $model = Presenter::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->with(['media', 'meta']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('submission.id')
            ->groupingSettingsHidden()
            ->groups([
                Group::make('submission.id')
                    ->label('Group by Submission')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn (Presenter $record): string => 'Submission : '.ucfirst($record->submission->getMeta('title')))
                    ->collapsible(),
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
                        ->circular(),
                    Stack::make([
                        TextColumn::make('fullName')
                            ->suffix(function (Model $record) {
                                $country = Country::find($record->getMeta('country'));

                                return ' '.$country?->flag;
                            })
                            ->formatStateUsing(function (Model $record) {
                                if ($record->email == auth()->user()->email) {
                                    return $record->fullName.' (You)';
                                }

                                return $record->fullName;
                            })
                            ->searchable(
                                query: fn ($query, $search) => $query
                                    ->whereMeta('country', 'LIKE', "%{$search}%")
                                    ->orWhere('given_name', 'LIKE', "%{$search}%")
                                    ->orWhere('family_name', 'LIKE', "%{$search}%")
                            ),
                        TextColumn::make('affiliation')
                            ->size('xs')
                            ->getStateUsing(
                                fn (Model $record) => $record->getMeta('affiliation')
                            )
                            ->icon('heroicon-o-building-library')
                            ->searchable(
                                query: fn ($query, $search) => $query
                                    ->whereMeta('affiliation', 'LIKE', "%{$search}%")
                            )
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->color('gray'),
                        TextColumn::make('email')
                            ->size('xs')
                            ->extraAttributes([
                                'class' => 'text-xs',
                            ])
                            ->searchable()
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->alignStart(),
                    ])
                    ->space(1),
                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(function (Model $record) {
                            return $record->status->getColor();
                        })
                        ->searchable()
                        ->alignCenter(),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->modalHeading('Approve Presenter')
                    ->icon('heroicon-o-check')
                    ->iconSize('md')
                    ->color('primary')
                    ->form([
                        Select::make('timeline')
                            ->label('Timeline')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Timeline::query()
                                    ->get()
                                    ->pluck('title', 'id')
                                    ->toArray();
                            }),
                        Textarea::make('note')
                            ->label('Note')
                            ->required()
                            ->placeholder('Enter your note here...')
                            ->helperText('This note will be sent to and seen by the presenter.')
                            ->rows(3),
                        Checkbox::make('do-not-notify-presenter')
                            ->label("Don't Send Notification to Presenter"),
                    ])
                    ->action(fn (Presenter $presenter) => $presenter->update(['status' => PresenterStatus::Approve])),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->modalHeading('Reject Presenter')
                    ->icon('heroicon-o-x-mark')
                    ->iconSize('md')
                    ->color('danger')
                    ->form([
                        Textarea::make('recommendation')
                            ->label('Recommendation')
                            ->required()
                            ->placeholder('Enter your recommendation here...')
                            ->helperText('This note will be sent to and seen by the presenter.')
                            ->rows(3),
                        Checkbox::make('do-not-notify-presenter')
                            ->label("Don't Send Notification to Presenter"),
                    ])
                    ->successNotificationTitle('The presenter has been rejected.')
                    ->action(function (Tables\Actions\Action $action, array $data, Presenter $record) {
                        $presenter = Presenter::find($action->getRecordId());

                        $presenter->update([
                            'status' => PresenterStatus::Reject,
                        ]);

                        if (! $data['do-not-notify-presenter']) {
                            try {
                                // Mail::to($record->email)
                                //     ->send(
                                //         (new RevisionRequestMail($this->submission))
                                //             ->subjectUsing($data['subject'])
                                //             ->contentUsing($data['message'])
                                //     );
                            } catch (\Exception $e) {
                                $action->failureNotificationTitle('The email notification was not delivered.');
                                $action->failure();
                            }
                        }

                        $action->success();
                    })
                
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePresenters::route('/'),
        ];
    }
}
