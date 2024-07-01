<?php

namespace App\Panel\Series\Resources;

use App\Facades\Setting;
use App\Models\Role;
use App\Models\Timeline;
use App\Panel\Series\Resources\TimelineResource\Pages;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Conference';

    public static function form(Form $form): Form
    {
        return $form->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('subtitle'),
                    DatePicker::make('date')
                        ->rule('date')
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            CheckboxList::make('roles')
                                ->options(Role::all()->pluck('name', 'name'))
                                ->columns(2),
                        ]),
                ]),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Timeline::query())
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('date')
                    ->dateTime(Setting::get('format_date'))
                    ->sortable(),
                TextColumn::make('roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Author' => 'warning',
                        'Reviewer' => 'gray',
                        'Participant' => 'primary',
                        'Editor' => 'gray',
                        default => 'primary'
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->filters([]);
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
            'index' => Pages\ManageTimeline::route('/'),
        ];
    }
}
