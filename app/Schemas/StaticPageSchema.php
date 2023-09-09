<?php

namespace App\Schemas;

use App\Actions\StaticPages\StaticPageCreateAction;
use App\Actions\StaticPages\StaticPageUpdateAction;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use App\Models\StaticPage;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Illuminate\Support\Str;

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
                                // ->afterStateUpdated(fn ($set, $state) => $set('path', Str::slug($state)))
                                ->helperText(function ($state, $record) {
                                    $staticPage = StaticPage::WhereMeta('path', Str::slug($state))->first('id');
                                    
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
                                ->default(fn () => auth()->user()->email)
                                ->disabled(),
                            TagsInput::make('tags')
                                ->disabled()
                        ])->columnSpan(3),
                ]),
        ];
    }
}
