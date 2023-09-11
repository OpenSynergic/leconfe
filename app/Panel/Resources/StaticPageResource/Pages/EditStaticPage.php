<?php

namespace App\Panel\Resources\StaticPageResource\Pages;

use App\Actions\StaticPages\StaticPageUpdateAction;
use App\Models\User;
use App\Panel\Resources\StaticPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return StaticPageUpdateAction::run($data, $record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $userContentMeta = $this->record->getAllMeta();
        $user = User::where('id', $userContentMeta['author'] ?? 0)->first();

        $data['author'] = $user ? "{$user->given_name} {$user->family_name}" : null;
        $data['common_tags'] = $this->record->tags()->pluck('id')->toArray();
        $data['path'] = $userContentMeta['path'];
        $data['user_content'] = $userContentMeta['user_content'];

        return $data;
    }
}
