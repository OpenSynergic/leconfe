<?php

namespace App\Panel\Administration\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\DeleteAction;
use App\Panel\Administration\Resources\ConferenceResource\Pages\ListConferences;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use App\Panel\Administration\Resources\ConferenceResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    public static function canAccess(): bool
    {
        return Auth::user()?->can('Administration:view') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('general.conference');
    }

    public static function getModelLabel(): string
    {
        return __('general.conference');
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-window';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('path')
                    ->label(__('general.path'))
                    ->helperText(__('general.url_path'))
                    ->required()
                    ->rule('alpha_dash')
                    ->unique(ignoreRecord: true)
                    ->prefix(config('app.url').'/'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (Conference $record) => route('filament.conference.pages.dashboard', $record))
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('name')
                    ->wrap()
                    ->label(__('general.name'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->button()
                    ->mutateRecordDataUsing(function (Conference $record, array $data) {
                        $data['meta'] = $record->getAllMeta()->toArray();

                        return $data;
                    })
                    ->using(fn (Conference $record, array $data) => ConferenceUpdateAction::run($record, $data)),
                DeleteAction::make()
                    ->button(),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => ListConferences::route('/'),
            // 'create' => Pages\CreateConference::route('/create'),
            // 'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
