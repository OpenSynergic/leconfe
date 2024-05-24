<?php

namespace App\Panel\Conference\Resources;

use App\Actions\Series\SerieSetAsActiveAction;
use App\Actions\Series\SerieSetAsCurrentAction;
use App\Facades\Settings;
use App\Models\Enums\SerieState;
use App\Models\Enums\SerieType;
use App\Models\Serie;
use App\Panel\Conference\Resources\SerieResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SerieResource extends Resource
{
    protected static ?string $model = Serie::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->autofocus()
                            ->autocomplete()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('path', Str::slug($state)))
                            ->placeholder('Enter the title of the serie'),
                        TextInput::make('path')
                            ->label('Path')
                            ->rule('alpha_dash')
                            ->required()
                            ->placeholder('Enter the path of the serie'),
                    ]),
                TextInput::make('issn')
                    ->label('ISSN')
                    ->placeholder('Enter the ISSN of the serie'),
                Grid::make()
                    ->schema([
                        DatePicker::make('date_start')
                            ->label('Start Date')
                            ->placeholder('Enter the start date of the serie')
                            ->requiredWith('date_end'),
                        DatePicker::make('date_end')
                            ->label('End Date')
                            ->afterOrEqual('date_start')
                            ->requiredWith('date_start')
                            ->placeholder('Enter the end date of the serie'),
                    ]),
                Select::make('type')
                    ->required()
                    ->options(SerieType::array()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Serie $record) => route('filament.series.pages.dashboard', ['serie' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn(Serie $record) => $record->current ? 'Current' : null)
                    ->sortable()
                    ->wrap()
                    ->wrapHeader(),
                TextColumn::make('date_start')
                    ->date(Settings::get('format_date')),
                TextColumn::make('date_end')
                    ->date(Settings::get('format_date')),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('publish')
                        ->label('Publish')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-up-on-square')
                        ->color('primary')
                        ->form([
                            Checkbox::make('set_as_current')
                                ->label('Set as current serie'),
                        ])
                        ->hidden(fn (Serie $record) => $record->isArchived() || $record->isCurrent() || $record->isPublished() || $record->trashed())
                        ->action(function (Serie $record, array $data, Tables\Actions\Action $action){
                            $data['set_as_current'] ? $record->update(['state' => SerieState::Current]) : $record->update(['state' => SerieState::Published]);

                            return $action->success();
                        }),
                    Tables\Actions\Action::make('set_as_current')
                        ->label('Set As Current')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(fn (Serie $record) => $record->isArchived() || $record->isCurrent() || $record->isDraft() || $record->trashed())
                        ->action(fn (Serie $record, Tables\Actions\Action $action) => $record->update(['state' => SerieState::Current]) && $action->success())
                        ->successNotificationTitle(fn(Serie $serie) => $serie->title . ' is set as current'),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn (Serie $record) => $record->isArchived() || $record->trashed()),
                    // Tables\Actions\DeleteAction::make()
                    //     ->label('Move To Trash')
                    //     ->modalHeading('Move To Trash')
                    //     ->hidden(fn (Serie $record) => $record->current || $record->trashed())
                    //     ->successNotificationTitle('Serie moved to trash'),
                ]),
                // Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSeries::route('/'),
        ];
    }
}
