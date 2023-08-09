<?php

namespace App\Schemas;

use App\Actions\Conferences\ConferenceChangeStatusAction;
use App\Actions\Conferences\ConferenceSetCurrentAction;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Squire\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class ConferenceSchema
{
    public static function table(Table $table): Table
    {
        $currentConference  = Conference::current();
        $query              = Conference::query();


        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->grow(false),
                TextColumn::make('name')
                    ->searchable(),
                // TextColumn::make('meta.short_description')
                //     ->toggleable()
                //     ->getStateUsing(fn (Conference $record) => $record->getMeta('short_description'))
                //     ->searchable(query: function (Builder $query, string $search): Builder {
                //         return $query
                //             ->whereMeta('title', 'like', "%{$search}%");
                //     }),
                TextColumn::make('status')
                    ->badge(),
                IconColumn::make('current')
                    ->getStateUsing(
                        fn ($record) => $record->getKey() == $currentConference?->getKey()
                    )
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('set_as_current')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->hidden(fn ($record) =>  $record->getKey() == $currentConference?->getKey())
                        ->successNotificationTitle(fn () => "Current conference is changed")
                        ->visible(function (Conference $record) use ($currentConference): bool {
                            if ($record->getKey() == $currentConference?->getKey()) return false;


                            return $record->status == ConferenceStatus::Active;
                        })
                        ->action(function ($record, Tables\Actions\Action $action) {
                            try {
                                ConferenceSetCurrentAction::run($record);
                            } catch (\Throwable $th) {
                                $action->failure();
                                return;
                            }

                            $action->success();
                        }),

                    Tables\Actions\Action::make('archive_conference')
                        ->requiresConfirmation()
                        ->visible(function (Conference $record) use ($currentConference): bool {
                            if ($record->getKey() == $currentConference?->getKey()) return false;

                            return $record->status == ConferenceStatus::Active;
                        })
                        ->color('warning')
                        ->icon('heroicon-s-archive-box-arrow-down')
                        ->action(function ($record, Tables\Actions\Action $action) {
                            try {
                                ConferenceChangeStatusAction::run($record, ConferenceStatus::Archived);
                            } catch (\Throwable $th) {
                                $action->failure();
                                return;
                            }

                            $action->success();
                        }),
                    Tables\Actions\EditAction::make()
                        ->modalWidth('2xl')
                        ->visible(function (Conference $record) use ($currentConference): bool {
                            return $record->status == ConferenceStatus::Active;
                        })
                        ->mutateRecordDataUsing(function (Conference $record, array $data) {
                            $data['meta'] = $record->getAllMeta();
                            $data['current'] = $record->getKey() == setting('current_conference');

                            return $data;
                        })
                        ->form(static::formSchemas())
                        ->using(fn (Conference $record, array $data) => ConferenceUpdateAction::run($data, $record)),
                    Tables\Actions\DeleteAction::make()
                        ->visible(function (Conference $record) use ($currentConference): bool {
                            if ($record->getKey() == $currentConference?->getKey()) return false;


                            return $record->status == ConferenceStatus::Active;
                        }),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas())
            ->columns(1);
    }

    public static function formSchemas(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('logo')
                ->collection('logo')
                ->image()
                ->conversion('thumb'),
            TextInput::make('name')
                ->required(),
            Textarea::make('meta.short_description'),
            Grid::make()
                ->schema([
                    TextInput::make('meta.publisher_name'),
                    TextInput::make('meta.affiliation'),
                    TextInput::make('meta.abbreviation'),
                    Select::make('meta.country')
                        ->searchable()
                        ->options(Country::pluck('name', 'id'))
                        ->optionsLimit(250),
                ]),
            Checkbox::make('current')
                ->label('Set this conference as the currently active one')
        ];
    }
}
