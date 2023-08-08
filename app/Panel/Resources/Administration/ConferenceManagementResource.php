<?php

namespace App\Panel\Resources\Administration;

use App\Actions\Conferences\ConferenceSetCurrentAction;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Panel\Resources\Administration\ConferenceManagementResource\Pages;
use App\Panel\Resources\Administration\ConferenceManagementResource\RelationManagers;
use App\Models\Conference;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Squire\Models\Country;

class ConferenceManagementResource extends Resource
{
    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $model = Conference::class;

    protected static ?string $navigationLabel = "Conference Management";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                Grid::make()
                    ->schema([
                        TextInput::make('meta.publisher_name'),
                        TextInput::make('meta.affiliation'),
                        TextInput::make('meta.abbreviation'),
                        Select::make('meta.country')
                            ->searchable()
                            ->required()
                            ->optionsLimit(250)
                            ->options(fn () => Country::all()->pluck('name', 'id')),
                    ]),
                Checkbox::make('current')
                    ->label('Set this conference as the currently active one')
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('current')
                    ->getStateUsing(
                        fn ($record) => $record->id == setting('current_conference')
                    )
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('current')
                        ->label('Set as current')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->hidden(fn ($record) =>  $record->id == setting('current_conference'))
                        ->successNotificationTitle(fn () => "Current conference is changed")
                        ->action(function ($record, Tables\Actions\Action $action) {
                            try {
                                ConferenceSetCurrentAction::run($record);
                            } catch (\Throwable $th) {
                                $action->failure();
                                return;
                            }

                            $action->success();
                        }),
                    Tables\Actions\EditAction::make()
                        ->modalWidth('2xl')
                        ->mutateRecordDataUsing(function (Conference $record, array $data) {
                            $data['meta'] = $record->getAllMeta();
                            $data['current'] = $record->id == setting('current_conference');

                            return $data;
                        })
                        ->using(fn (Conference $record, array $data) => ConferenceUpdateAction::run($data, $record)),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConferenceManagement::route('/'),
        ];
    }
}
