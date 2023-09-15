<?php

namespace App\Schemas;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Actions\StaticPages\StaticPageUpdateAction;
use App\Forms\Components\TagSuggestions;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use App\Models\StaticPage;
use App\Models\StaticPageTag;
use App\Models\Tag;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;

class StaticPageSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Filament::getTenant()->staticPages()->with(['conference'])->getQuery())
            ->heading('Static page')
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn ($record) => route('livewirePageGroup.website.pages.static-page', [
                'slug' => $record->slug
            ]))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('path')
                    ->getStateUsing(fn (StaticPage $record) => route('livewirePageGroup.website.pages.static-page', [
                        'slug' => $record->slug
                    ])),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('livewirePageGroup.website.pages.static-page', [
                        'slug' => $record->slug
                    ]))
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
                                ->lazy()
                                ->helperText(function ($state, ?StaticPage $record) {
                                    $slug = Str::slug($state);
                                    $currentSlug = $slug;
                                    $count = 1;

                                    switch (true) {
                                        case $record:
                                            while (true) {
                                                $staticPage = StaticPage::where('slug', $currentSlug)->first();
                                                if ($staticPage) {
                                                    if ($staticPage->id != $record->id) {
                                                        $currentSlug = "{$slug}-{$count}";
                                                        $count++;
                                                    } else {
                                                        break;
                                                    }
                                                } else {
                                                    break;
                                                }
                                            }
                                            break;
                                        
                                        default:
                                            while (true) {
                                                $staticPage = StaticPage::where('slug', $currentSlug)->first();
                                                // dd($staticPage);
                                                if ($staticPage) {
                                                    $currentSlug = "{$slug}-{$count}";
                                                    $count++;
                                                } else {
                                                    break;
                                                }
                                            }
                                            break;
                                    }

                                    $route = route('livewirePageGroup.website.pages.static-page', [
                                        'slug' => $currentSlug ? ($currentSlug != '' ? $currentSlug : 'path-name') : 'path-name'
                                    ]);
                
                                    return new HtmlString("
                                        <p>Your page will be at :</p>
                                        <p>\"{$route}\"</p>
                                    ");
                                })
                                ->required(),
                            TinyEditor::make('user_content')
                                ->label('Content')
                                ->minHeight(600)
                                ->helperText('The complete page content.'),
                        ])->columnSpan(9),
                    Section::make()
                        ->schema([
                            TextInput::make('author')
                                ->default(function () {
                                    $user = auth()->user();
                                    return "{$user->given_name} {$user->family_name}";
                                })
                                ->dehydrated(false)
                                ->disabled(),
                            SpatieTagsInput::make('tags')
                                ->type(ContentType::StaticPage->value)
                                ->afterStateUpdated(fn ($set, $state) => $set('common_tags', StaticPageTag::whereInFromString($state, ContentType::StaticPage->value)->pluck('id')->toArray()))
                                ->reactive(),
                            TagSuggestions::make('common_tags')
                                ->label('Commonly used tags')
                                ->helperText(fn (CheckboxList $component) => count($component->getOptions()) ? null : 
                                    new HtmlString('
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
                                    ')
                                )
                                ->options(StaticPageTag::withCount('staticPages')->orderBy('static_pages_count', 'desc')->limit(10)->pluck('name', 'id')->toArray())
                                ->columns('2')
                                ->afterStateUpdated(function ($set, $state) {
                                    if (!empty($state)) {
                                        $state = StaticPageTag::whereIn('id', $state)->get()->map(fn ($tag) => $tag->name)->toArray();
                                    }
    
                                    $set('tags', $state);
                                })
                                ->dehydrated(false)
                                ->reactive(),
                        ])->columnSpan(3),
                ]),
        ];
    }
}
