<?php

namespace App\Schemas;

use App\Models\Announcement;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class AnnouncementSchema
{
    public static function table(Table $table): Table
    {
        // dd(Announcement::first()->expires_at->__toString());

        return $table
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('expires_at')
                    ->date(setting('format.date')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
                EditAction::make(),
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
            TextInput::make('title')
                ->required(),
            TinyEditor::make('short_description')
                ->helperText('A concise overview intended for display alongside the announcement heading.'),
            TinyEditor::make('announcement')
                ->helperText('The complete textual content of the announcement.'),
            Flatpickr::make('expires_at')
                // ->dateFormat(setting('format.date'))
                ->formatStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }

                    return Carbon::parse($state)
                        ->translatedFormat(setting('format.date'));
                })
                ->minDate(today()->subDay())
                ->dehydrateStateUsing(fn ($state) => Carbon::createFromFormat(setting('format.date'), $state)),
            Checkbox::make('send_email')
                ->label('Send email about this to registered users'),
        ];
    }
}
