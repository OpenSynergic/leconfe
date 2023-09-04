<?php

namespace App\Schemas;

use App\Actions\UserContents\UserContentCreateAction;
use App\Actions\UserContents\UserContentUpdateAction;
use App\Models\Enums\ConferenceStatus;
use App\Models\Enums\ContentType;
use App\Models\UserContent;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class StaticPageSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Filament::getTenant()->staticPages()->with(['conference'])->getQuery())
            ->heading('Static page')
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn ($record) => static::url($record))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->label('Description')
                    ->getStateUsing(fn (UserContent $record) => new HtmlString($record->getMeta('short_description') != '' ? $record->getMeta('short_description') : 'No description added')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('2xl')
                    ->label('Add static page')
                    ->outlined()
                    ->form(fn () => static::formSchemas())
                    ->mutateFormDataUsing(function ($data) {
                        $data['content_type'] = ContentType::StaticPage;

                        return $data;
                    })
                    ->using(fn (array $data) => UserContentCreateAction::run($data)),
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => static::url($record))
                    ->color('gray'),
                EditAction::make()
                    ->using(fn (UserContent $record, $data) => UserContentUpdateAction::run($data, $record))
                    ->form(static::formSchemas())
                    ->mutateRecordDataUsing(function ($data, $record) {
                        $userContentMeta = $record->getAllMeta();

                        $data['short_description'] = $userContentMeta['short_description'];
                        $data['user_content'] = $userContentMeta['user_content'];

                        return $data;
                    }),
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
                ->helperText('A concise overview of your page.'),
            TinyEditor::make('user_content')
                ->label('Announcement content')
                ->helperText('The complete page content.'),
        ];
    }

    public static function url($record) {
        $conference  = $record->conference;
        $contentType = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $record->content_type)), '-');

        switch ($conference->status->value) {
            case ConferenceStatus::Current->value:
                return 
                    route('livewirePageGroup.current-conference.pages.static-page', [
                        'content_type' => $contentType,
                        'user_content' => $record->id
                    ]);
                break;
            
            default:
                return
                    route('livewirePageGroup.archive-conference.pages.static-page', [
                        'conference' => $conference->id,
                        'content_type' => $contentType,
                        'user_content' => $record->id
                    ]);
                break;
        }
    }
}
