<?php

namespace App\Administration\Resources\StaticPageResource\Pages;

use App\Actions\StaticPages\StaticPageUpdateAction;
use App\Administration\Resources\StaticPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->icon('heroicon-o-eye')
                ->label('Preview')
                ->color('success')
                ->url(fn ($record) => $record->getUrl()),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return StaticPageUpdateAction::run($data, $record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $userContentMeta = $this->record->getAllMeta();
        $user = $this->record->user;

        $data['author'] = $user ? $user->full_name : 'Cannot find the author';
        $data['common_tags'] = $this->record->tags()->pluck('id')->toArray();
        $data['user_content'] = $userContentMeta['user_content'];

        return $data;
    }
}
