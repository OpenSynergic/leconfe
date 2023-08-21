<?php

namespace App\Panel\Resources\Conferences\CommitteeResource\Pages;

use App\Actions\Committee\CommitteMemberInsertAction;
use App\Panel\Resources\Conferences\CommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageCommittee extends ManageRecords
{
    protected static string $resource = CommitteeResource::class;

    protected static string $view = 'panel.resources.committee-resource.view-committee';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->using(fn (array $data) => CommitteMemberInsertAction::run($data)),
    //     ];
    // }

    // public function getTabs(): array
    // {
    //     return [
    //         'member' => Tab::make(),
    //     ];
    // }
}
