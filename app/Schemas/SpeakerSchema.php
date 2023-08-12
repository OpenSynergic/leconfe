<?php

namespace App\Schemas;

use App\Models\Speaker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Actions\Conferences\CreateSpeakerAction;
use Filament\Infolists\Components\Grid as GridInfolist;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class SpeakerSchema
{
    public static function table(Table $table): Table
    {
        return $table
            // ->heading('Speaker')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('speaker_photos')
                    ->width(50)
                    ->height(50)
                    ->circular()
                    ->extraImgAttributes([
                        'style' => 'box-shadow: 0px 20px 50px -10px rgba(0, 0, 0, 0.3);'
                    ]),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('position'),
                TextColumn::make('affiliation',)
            ])
            ->reorderable('order_column')
            // ->headerActions([
            //     CreateAction::make()
            //         ->modalWidth('2xl')
            //         ->form(static::formSchemas())
            //         ->using(fn (array $data) => CreateSpeakerAction::run($data))
            // ])
            ->actions([
                ViewAction::make()
                    ->infolist(static::infoListSchemas())
                    ->modalHeading(''),
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth('2xl')
                        ->form(static::formSchemas()),
                    DeleteAction::make()
                ])
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
            Grid::make(1)
                ->schema([
                    SpatieMediaLibraryFileUpload::make('photo')
                        ->avatar()
                        ->alignCenter()
                        ->collection('speaker_photos')
                        ->label(''),
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('position')
                        ->placeholder('Keynote Speaker'),
                    TextInput::make('affiliation'),
                    Textarea::make('description'),
                ])
        ];
    }

    public static function infoListSchemas(): array
    {
        return [
            GridInfolist::make([
                'default' => 12
            ])
                ->schema([
                    SpatieMediaLibraryImageEntry::make('photo')
                        ->collection('speaker_photos')
                        ->circular()
                        ->alignCenter()
                        ->label('')
                        ->columnSpan(2),
                    GridInfolist::make()
                        ->schema([
                            TextEntry::make('name')
                                ->weight(FontWeight::Bold)
                                ->label('Name'),
                            TextEntry::make('affiliation')
                                ->label('')
                                ->color('secondary')
                                ->icon('heroicon-o-building-library'),
                        ])
                        ->columnSpan(9),
                ]),
            Section::make([
                TextEntry::make('description')
                    ->color('secondary')
            ])
        ];
    }
}
