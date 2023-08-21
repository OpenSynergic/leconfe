<?php

namespace App\Panel\Resources\Conferences\CommitteeResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use App\Infolists\Components\LivewireEntry;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Actions\Committee\CommitteMemberInsertAction;
use App\Livewire\CommitteeMemberTable;
use App\Panel\Resources\Conferences\CommitteeResource;

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