<?php

namespace App\Panel\Conference\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use App\Panel\Conference\Resources\ScheduledConferenceResource\Pages\ManageScheduledConferences;
use App\Actions\ScheduledConferences\ScheduledConferenceUpdateAction;
use App\Models\ScheduledConference;
use App\Panel\Conference\Resources\ScheduledConferenceResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Jackiedo\Timezonelist\Facades\Timezonelist;

class ScheduledConferenceResource extends Resource
{
    protected static ?string $model = ScheduledConference::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    public static function getNavigationLabel(): string
    {
        return __('general.scheduled_conference');
    }

    public static function getModelLabel(): string
    {
        return __('general.scheduled_conference');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                TextInput::make('title')
                    ->label(__('general.title'))
                    ->autofocus()
                    ->autocomplete()
                    ->required()
                    ->placeholder(__('general.enter_the_title_of_the_serie')),
                TextInput::make('path')
                    ->prefix(fn () => route('livewirePageGroup.conference.pages.home', ['conference' => app()->getCurrentConference()->path]).'/scheduled/')
                    ->label(__('general.path'))
                    ->rules(['alpha_dash'])
                    ->unique(modifyRuleUsing: fn (Unique $rule) => $rule->where('conference_id', app()->getCurrentConferenceId()), ignoreRecord: true)
                    ->required(),
                Grid::make()
                    ->schema([
                        DatePicker::make('date_start')
                            ->label(__('general.start_date'))
                            ->placeholder(__('general.enter_the_start_date_of_the_serie'))
                            ->requiredWith('date_end'),
                        DatePicker::make('date_end')
                            ->label(__('general.end_date'))
                            ->afterOrEqual('date_start')
                            ->placeholder(__('general.enter_the_end_date_of_the_serie')),
                    ]),
                Select::make('meta.timezone')
                    ->label(__('general.timezone'))
                    ->options(Timezonelist::toArray(false))
                    ->optionsLimit(500)
                    ->default(config('app.timezone', 'UTC'))
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (ScheduledConference $record) => route('filament.scheduledConference.pages.dashboard', ['serie' => $record]))
            ->defaultSort('date_start', 'desc')
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('title')
                    ->label(__('general.title'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->wrapHeader(),
                TextColumn::make('status')
                    ->getStateUsing(fn ($record) => match ($record->is_published) {
                        true => __('general.published'),
                        false => __('general.draft'),
                    })
                    ->color(fn ($record) => match ($record->is_published) {
                        true => 'success',
                        false => 'gray',
                    })
                    ->badge(),
                TextColumn::make('date_start')
                    ->getStateUsing(fn ($record) => $record->full_date)
                    ->sortable()
                    ->label(__('general.date'))
                    ->wrap(),
                TextColumn::make('submitted_submissions_count')
                    ->label(__('general.submissions'))
                    ->counts('submittedSubmissions'),
                TextColumn::make('participants_count')
                    ->label(__('general.participants'))
                    ->counts('participants'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('publish')
                        ->label(__('general.publish'))
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-up-on-square')
                        ->color('success')
                        ->hidden(fn (ScheduledConference $record) => $record->is_published)
                        ->action(function (ScheduledConference $record, array $data, Action $action) {
                            ScheduledConferenceUpdateAction::run($record, ['is_published' => true]);

                            return $action->success();
                        }),
                    EditAction::make()
                        ->modalWidth(Width::ExtraLarge)
                        ->hidden(fn (ScheduledConference $record) => $record->trashed())
                        ->mutateRecordDataUsing(function (ScheduledConference $record, array $data) {
                            $data['meta'] = $record->getAllMeta()->toArray();

                            return $data;
                        })
                        ->using(fn (ScheduledConference $record, array $data) => ScheduledConferenceUpdateAction::run($record, $data)),
                    Action::make('set_as_draft')
                        ->label(__('general.set_as_draft'))
                        ->requiresConfirmation()
                        ->icon('heroicon-o-pencil-square')
                        ->hidden(fn (ScheduledConference $record) => ! $record->is_published || $record->trashed())
                        ->action(fn (ScheduledConference $record, Action $action) => $record->update(['is_published' => false]) && $action->success()),
                    DeleteAction::make()
                        ->label(__('general.move_to_trash'))
                        ->modalHeading(__('general.move_to_trash'))
                        ->hidden(fn (ScheduledConference $record) => $record->trashed())
                        ->successNotificationTitle(__('general.serie_moved_to_trash')),
                    ForceDeleteAction::make()
                        ->label(__('general.delete_permanently'))
                        ->hidden(fn (ScheduledConference $record) => ! $record->trashed())
                        ->successNotificationTitle(__('general.serie_deleted_permanently')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageScheduledConferences::route('/'),
        ];
    }
}
