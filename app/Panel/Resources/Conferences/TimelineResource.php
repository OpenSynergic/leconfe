<?php

namespace App\Panel\Resources\Conferences;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use App\Models\Timeline;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Schemas\TopicSchema;
use App\Schemas\TimelineSchema;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use App\Panel\Resources\Conferences\TimelineResource\Pages;
use App\Panel\Resources\Conferences\TimelineResource\RelationManagers;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;

class TimelineResource extends Resource
{
    protected static ?string $model = Timeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Conferences';


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
                    Flatpickr::make('date')
                        ->rule('date')
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            CheckboxList::make('roles')
                                ->options(Role::all()->pluck('name', 'name'))
                                ->columns(2),
                        ])
                ]),

        ];
    }

    public static function table(Table $table): Table
    {
        return  $table
            ->query(Timeline::query())
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('date')
                    ->dateTime(setting('format.date'))
                    ->sortable(),
                TextColumn::make('roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Author' => 'warning',
                        'Reviewer' => 'gray',
                        'Participant' => 'primary',
                        'Editor' => 'gray',
                        default => 'primary'
                    })
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        // costumize date format before filling the form
                        ->mutateRecordDataUsing(function (array $data): array {
                            $dateFormat = date(setting('format.date'), strtotime($data['date']));
                            $data['date'] = $dateFormat;
                            return $data;
                        })
                        // costumize date format before saving to database
                        ->mutateFormDataUsing(function (array $data): array {
                            $dateFormat = date('Y-m-d', strtotime($data['date']));
                            $data['date'] = $dateFormat;
                            return $data;
                        }),
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
