<?php

namespace App\Schemas;

use App\Models\Topic;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use App\Actions\Conferences\CreateTopicAction;

class TopicSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Topic::query())
            ->heading('Topic')
            ->columns([
                TextColumn::make('name'),



            ])

            ->filters([])

            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->form(static::formSchemas())
                    ->using(fn (array $data) => CreateTopicAction::run($data)),
            ])

            ->actions([
                ViewAction::make()
                    ->form(static::formSchemas()),
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(fn () => static::formSchemas()),
                    DeleteAction::make()
                ]),


            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('name')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->required(),
                    TextInput::make('slug')
                        ->required(),
                ])
        ];
    }
}
