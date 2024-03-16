<?php

namespace App\Panel\Resources\Conferences;

use App\Forms\Components\TagSuggestions;
use App\Models\Announcement;
use App\Models\AnnouncementTag;
use App\Models\Enums\ContentType;
use App\Panel\Resources\Conferences\AnnouncementResource\Pages;
use App\Panel\Resources\Traits\CustomizedUrl;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $modelLabel = 'Announcement';

    protected static ?string $navigationGroup = 'Conferences';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    use CustomizedUrl;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['conference']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('title')
                                    ->required(),
                                TinyEditor::make('meta.content')
                                    ->toolbarSticky(true)
                                    ->label('Announcement')
                                    ->minHeight(600)
                                    ->helperText('The complete announcement content.'),
                                Checkbox::make('send_email')
                                    ->label('Send email about this to subscribed users')
                                    ->hidden(fn (?Announcement $record) => $record),
                            ])->columnSpan([
                                'default' => 'full',
                                'lg' => 9,
                            ]),
                        Section::make()
                            ->schema([
                                TextInput::make('author')
                                    ->default(function () {
                                        $user = auth()->user();

                                        return $user->full_name;
                                    })
                                    ->dehydrated(false)
                                    ->disabled(),
                                SpatieTagsInput::make('tags')
                                    ->type(ContentType::Announcement->value)
                                    ->afterStateUpdated(fn ($set, $state) => $set('common_tags', AnnouncementTag::whereInFromString($state, ContentType::Announcement->value)->pluck('id')->toArray()))
                                    ->reactive(),
                                TagSuggestions::make('common_tags')
                                    ->label('Commonly used tags')
                                    ->helperText(function (TagSuggestions $component) {
                                        if (count($component->getOptions())) {
                                            return null;
                                        }

                                        return new HtmlString(<<<'HTML'
                                            <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                                                <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                                    <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            
                                                <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                                    No tags
                                                </h4>
                                            </div>
                                            HTML);
                                    })
                                    ->options(AnnouncementTag::withCount('announcements')->orderBy('announcements_count', 'desc')->limit(10)->pluck('name', 'id')->toArray())
                                    ->afterStateUpdated(function ($set, $state) {
                                        if (!empty($state)) {
                                            $state = AnnouncementTag::whereIn('id', $state)->get()->map(fn ($tag) => $tag->name)->toArray();
                                        }

                                        $set('tags', $state);
                                    })
                                    ->dehydrated(false)
                                    ->reactive(),
                                SpatieMediaLibraryFileUpload::make('featured_image')
                                    ->collection('featured_image')
                                    ->image(),
                                DatePicker::make('meta.expires_at')
                                    ->minDate(today()->subDay()),
                            ])->columnSpan([
                                'default' => 'full',
                                'lg' => 3,
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                        $conference = $record->conference;

                        return route('livewirePageGroup.conference.pages.announcement-page', [
                            'conference' => $conference->path,
                            'announcement' => $record->id,
                        ]);
                    })
                    ->color('gray'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
