<?php

namespace App\Panel\Resources\Conferences;

use App\Models\Participants\Speaker;
use App\Panel\Resources\Conferences\SpeakerResource\Pages;
use App\Panel\Resources\Conferences\SpeakerResource\Widgets;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpeakerResource extends Resource
{
    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('position')
            ->orderBy('order_column');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TODO : Add search field to select existing speaker. Search across all conferences
                SpatieMediaLibraryFileUpload::make('photo')
                    ->image()
                    ->key('photo')
                    ->collection('photo')
                    ->conversion('thumb')
                    ->alignCenter()
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                TextInput::make('given_name')
                    ->required(),
                TextInput::make('family_name'),
                TextInput::make('email')
                    ->email()
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                Select::make('participant_position_id')
                    ->required()
                    ->relationship('position', 'name')
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionAction(
                        fn (FormAction $action) => $action->modalWidth('xl')
                            ->modalHeading('Create Speaker Position')
                            ->form(function (Select $component, Form $form): array|Form|null {
                                return SpeakerPositionResource::form($form);
                            })
                    )
                    ->columnSpan([
                        'lg' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            // Disable because grouping with reorderable active is acting weird
            // ->groups([
            //     Group::make('position.name')
            //         ->label('Position'),
            // ])
            ->heading('Speakers Table')
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                IndexColumn::make('no')
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->conversion('avatar')
                    ->width(50)
                    ->height(50)
                    ->extraCellAttributes([
                        'style' => 'width: 1px',
                    ])
                    ->circular()
                    ->toggleable(),
                TextColumn::make('given_name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('family_name'),
                TextColumn::make('position.name')
                    ->toggleable(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->filters([
                SelectFilter::make('position')
                    ->relationship('position', 'name'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSpeakers::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\SpeakerOverview::make(),
        ];
    }
}
