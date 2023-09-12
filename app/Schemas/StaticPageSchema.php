<?php

namespace App\Schemas;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Actions\StaticPages\StaticPageUpdateAction;
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
                'path' => $record->getMeta('path')
            ]))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('path')
                    ->label('Page url')
                    ->getStateUsing(fn (StaticPage $record) => route('livewirePageGroup.website.pages.static-page', [
                        'path' => $record->getMeta('path')
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
                        'path' => $record->getMeta('path')
                    ]))
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
            Grid::make(12)
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('title')
                                ->reactive()
                                ->helperText(function ($state, ?StaticPage $record) {
                                    $staticPage = StaticPage::WhereMeta('path', Str::slug($state))->first('id');

                                    // dd(SlugOptions::create(['title' => 'dwadwadwadwa'])->generateSlugsFrom('title')->saveSlugsTo('slug'));
                                    
                                    $isDumplicate = true;
                                    if ($record) {
                                        if ($staticPage->id == $record->id) {
                                            $isDumplicate = false;
                                        }
                                    }

                                    if ($staticPage && $isDumplicate) {
                                        $state .= '-1';
                                    }
                                    $route = route('livewirePageGroup.website.pages.static-page', [
                                        'path' => Str::slug($state) ? (Str::slug($state) != '' ? Str::slug($state) : 'path-name') : 'path-name'
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
                                ->disabled(),
                            SpatieTagsInput::make('tags')
                                ->type(ContentType::StaticPage->value)
                                ->afterStateUpdated(fn ($set, $state) => $set('common_tags', StaticPageTag::whereInFromString($state, ContentType::StaticPage->value)->pluck('id')->toArray()))
                                ->reactive(),
                            CheckboxList::make('common_tags')->label('Commonly used tags')
                                ->options(StaticPageTag::withCount('staticPages')->orderBy('static_pages_count', 'desc')->limit(10)->pluck('name', 'id')->toArray())
                                ->columns('2')
                                ->afterStateUpdated(function ($set, $state) {
                                    if (!empty($state)) {
                                        $state = StaticPageTag::whereIn('id', $state)->get()->map(fn ($tag) => $tag->name)->toArray();
                                    }
    
                                    $set('tags', $state);
                                })
                                ->reactive(),
                        ])->columnSpan(3),
                ]),
        ];
    }
}
