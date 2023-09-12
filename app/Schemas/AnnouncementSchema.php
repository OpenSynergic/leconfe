<?php

namespace App\Schemas;

use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Models\Announcement;
use App\Models\AnnouncementTag;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use App\Models\Tag;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
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

                        switch ($conference->status->value) {
                            case ConferenceStatus::Current->value:
                                return 
                                    route('livewirePageGroup.current-conference.pages.announcement-page', [
                                        'id' => $record->id
                                    ]);
                                break;
                            
                            default:
                                return
                                    route('livewirePageGroup.archive-conference.pages.announcement-page', [
                                        'conference' => $conference->id,
                                        'id' => $record->id
                                    ]);
                                break;
                        }
                    })
                    ->color('gray'),
                EditAction::make(),
                DeleteAction::make(),
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
            Grid::make(12)
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('title')
                                ->required(),
                            TinyEditor::make('user_content')
                                ->label('Announcement content')
                                ->minHeight(600)
                                ->helperText('The complete announcement content.'),
                        ])->columnSpan(9),
                    Section::make()
                        ->schema([
                            TextInput::make('author')
                                ->default(function () {
                                    $user = auth()->user();
                                    return "{$user->given_name} {$user->family_name}";
                                })
                                ->disabled(),
                            SpatieTagsInput::make('tags')
                                ->type(ContentType::Announcement->value)
                                ->afterStateUpdated(fn ($set, $state) => $set('common_tags', AnnouncementTag::whereInFromString($state, ContentType::Announcement->value)->pluck('id')->toArray()))
                                ->reactive(),
                            CheckboxList::make('common_tags')->label('Commonly used tags')
                                ->options(AnnouncementTag::withCount('announcements')->orderBy('announcements_count', 'desc')->limit(10)->pluck('name', 'id')->toArray())
                                ->columns('2')
                                ->afterStateUpdated(function ($set, $state) {
                                    if (!empty($state)) {
                                        $state = AnnouncementTag::whereIn('id', $state)->get()->map(fn ($tag) => $tag->name)->toArray();
                                    }
    
                                    $set('tags', $state);
                                })
                                ->reactive(),
                            SpatieMediaLibraryFileUpload::make('featured_image')
                                ->image(),
                            Flatpickr::make('expires_at')
                                ->required()
                                ->dateFormat(setting('format.date'))
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
                        ])->columnSpan(3),
                ]),
        ];
    }
}
