<?php

namespace App\Schemas;

use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Models\Announcement;
use App\Models\Enums\ConferenceStatus;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class AnnouncementSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['conference']))
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('expires_at')
                    ->date(setting('format.date'))
                    ->getStateUsing(fn (Announcement $record) => $record->getMeta('expires_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        $conference  = $record->conference;
                        // $contentType = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $record->content_type)), '-');

                        switch ($conference->status->value) {
                            case ConferenceStatus::Current->value:
                                return 
                                    route('livewirePageGroup.current-conference.pages.announcement-page', [
                                        'user_content' => $record->id
                                    ]);
                                break;
                            
                            default:
                                return
                                    route('livewirePageGroup.archive-conference.pages.announcement-page', [
                                        'conference' => $conference->id,
                                        'user_content' => $record->id
                                    ]);
                                break;
                        }
                    })
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
            Grid::make(5)
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->columnSpanFull(),
                            TinyEditor::make('user_content')
                                ->label('Announcement content')
                                ->helperText('The complete announcement content.')
                                ->columnSpanFull(),
                        ])->columnSpan(4),
                    Grid::make()
                        ->schema([
                            TinyEditor::make('short_description')
                                ->helperText('A concise overview intended to display alongside the announcement heading.')
                                ->columnSpanFull(),
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
                                ->dehydrateStateUsing(fn ($state) => Carbon::createFromFormat(setting('format.date'), $state))
                                ->columnSpanFull(),
                            Checkbox::make('send_email')
                                ->label('Send email about this to registered users')
                                ->columnSpanFull(),
                        ])->columnSpan(1),
                ]),
        ];
    }
}
