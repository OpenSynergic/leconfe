<?php

namespace App\Panel\ScheduledConference\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Panel\ScheduledConference\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Actions\Announcements\AnnouncementUpdateAction;
use App\Facades\Setting;
use App\Forms\Components\TinyEditor;
use App\Models\Announcement;
use App\Panel\ScheduledConference\Resources\AnnouncementResource\Pages;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $modelLabel = 'Announcement';

    public static function getNavigationGroup(): string
    {
        return __('general.conference');
    }

    public static function getModelLabel(): string
    {
        return __('general.announcement');
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-speaker-wave';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('general.title'))
                    ->required(),
                Textarea::make('meta.summary')
                    ->label(__('general.summary'))
                    ->autosize(),
                TinyEditor::make('meta.content')
                    ->label(__('general.announcement'))
                    ->profile('basic')
                    ->helperText(__('general.complete_announcement_content')),
                DatePicker::make('expires_at')
                    ->label(__('general.expires_at'))
                    ->minDate(today()->addDay()),
                Checkbox::make('send_email')
                    ->label(__('general.send_email_to_subscribed_users'))
                    ->hidden(fn (?Announcement $record) => $record),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('title')
                    ->label(__('general.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__('general.expires_at'))
                    ->sortable()
                    ->date(Setting::get('format_date')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('livewirePageGroup.scheduledConference.pages.announcement-page', [
                        'announcement' => $record->id,
                    ]))
                    ->color('gray'),
                EditAction::make()
                    ->mutateRecordDataUsing(function (Announcement $record, array $data) {
                        $data['meta'] = $record->getAllMeta()->toArray();

                        return $data;
                    })
                    ->using(fn (Announcement $record, array $data) => AnnouncementUpdateAction::run($record, $data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
        ];
    }
}
