<?php

namespace App\Panel\Conference\Resources\PresenterResource\Pages;

use App\Models\Enums\PresenterStatus;
use App\Models\Presenter;
use App\Panel\Conference\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManagePresenters extends ManageRecords
{
    protected static string $resource = PresenterResource::class;

    public function getTabs(): array
    {
        return [
            'unchecked' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PresenterStatus::Unchecked))
                ->badge(static::presenterTabQuery(PresenterStatus::Unchecked)->count())
                ->badgeColor(static::presenterTabQuery(PresenterStatus::Unchecked)->count() > 0 ? 'primary' : 'gray'),
            'approved' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PresenterStatus::Approve))
                ->badge(static::presenterTabQuery(PresenterStatus::Approve)->count())
                ->badgeColor(static::presenterTabQuery(PresenterStatus::Approve)->count() > 0 ? 'primary' : 'gray'),
            'rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PresenterStatus::Reject))
                ->badge(static::presenterTabQuery(PresenterStatus::Reject)->count())
                ->badgeColor(static::presenterTabQuery(PresenterStatus::Reject)->count() > 0 ? 'primary' : 'gray'),
        ];
    }

    public static function presenterTabQuery(PresenterStatus $status): Builder
    {
        return Presenter::whereStatus($status)->whereHas('submission', function ($query) {
            $query->where('conference_id', app()->getCurrentConference()->getKey());
        });
    }
}
