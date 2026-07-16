<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use App\Panel\ScheduledConference\Resources\CommitteeRoleResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Component;

class CommitteeRoleTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = CommitteeRoleResource::class;

    public function render()
    {
        return view('tables.table');
    }

    public function table(Table $table): Table
    {
        return static::$resource::table($table)
            ->query(fn (): Builder => static::getResource()::getEloquentQuery());
    }

    protected function configureTableAction(Action $action): void
    {
        match (true) {
            $action instanceof EditAction => $this->configureEditAction($action),
            $action instanceof CreateAction => $this->configureCreateAction($action),
            default => null,
        };
    }

    protected function configureEditAction(EditAction $action): void
    {
        $resource = static::getResource();
        $action
            ->authorize(fn (Model $record): bool => $resource::canEdit($record))
            ->form(fn (Schema $schema): Schema => $resource::form($schema))
            ->modalWidth('xl');

        if ($resource::hasPage('edit')) {
            $action->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]));
        }
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canCreate())
            ->model(static::getResource()::getModel())
            ->modelLabel(static::getResource()::getModelLabel())
            ->schema(fn (Schema $schema): Schema => $resource::form($schema))
            ->modalWidth('xl');

        if ($action instanceof CreateAction) {
            $action->relationship(($tenant = app()->getCurrentConference()) ? fn (): Relation => static::getResource()::getTenantRelationship($tenant) : null);
        }

        if ($resource::hasPage('create')) {
            $action->url(fn (): string => $resource::getUrl('create'));
        }
    }

    public static function getResource(): string
    {
        return static::$resource;
    }
}
