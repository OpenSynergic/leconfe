<?php

namespace App\Panel\ScheduledConference\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use App\Panel\ScheduledConference\Resources\TimelineResource\Pages\ManageTimeline;
use App\Models\Timeline;
use App\Panel\ScheduledConference\Resources\TimelineResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationGroup(): string
    {
        return __('general.conference');
    }

    public static function getModelLabel(): string
    {
        return __('general.timeline');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('general.description'))
                    ->maxLength(255),
                Grid::make()
                    ->schema([
                        DatePicker::make('date')
                            ->label(__('general.start_date'))
                            ->required(),
                        DatePicker::make('date_end')
                            ->label(__('general.end_date'))
                            ->after('date'),
                    ]),
                Select::make('type')
                    ->label(__('general.type'))
                    ->options(Timeline::getTypes())
                    ->helperText(__('general.type_integrates_with_workflow_process'))
                    ->unique(
                        ignorable: fn () => $schema->getRecord(),
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('scheduled_conference_id', app()->getCurrentScheduledConferenceId()),
                    )
                    ->native(false),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Timeline::query())
            ->heading(__('general.timeline'))
            ->defaultSort('date')
            ->columns([
                TextColumn::make('fullDate')
                    ->label(__('general.date'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('general.name')),
                ToggleColumn::make('hide')
                    ->label(__('general.hidden')),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge),
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
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
            'index' => ManageTimeline::route('/'),
        ];
    }
}
