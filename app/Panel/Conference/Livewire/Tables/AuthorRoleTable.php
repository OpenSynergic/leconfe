<?php

namespace App\Panel\Conference\Livewire\Tables;

// use App\Panel\Conference\Livewire\Traits\PlaceholderTrait;
use App\Panel\Conference\Resources\Conferences\AuthorRoleResource;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Component;

class AuthorRoleTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = AuthorRoleResource::class;

    public function render()
    {
        return view('panel.conference.livewire.tables.table');
    }

    public function table(Table $table): Table
    {
        return static::$resource::table($table)
            ->query(fn (): Builder => static::getResource()::getEloquentQuery());
    }

    protected function configureTableAction(Tables\Actions\Action $action): void
    {
        match (true) {
            $action instanceof Tables\Actions\EditAction => $this->configureEditAction($action),
            $action instanceof Tables\Actions\CreateAction => $this->configureCreateAction($action),
            default => null,
        };
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        $resource = static::getResource();
        $action
            ->authorize(fn (Model $record): bool => $resource::canEdit($record))
            ->form(fn (Form $form): Form => $resource::form($form))
            ->modalWidth('xl');

        if ($resource::hasPage('edit')) {
            $action->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]));
        }
    }

    protected function configureCreateAction(CreateAction|Tables\Actions\CreateAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canCreate())
            ->model(static::getResource()::getModel())
            ->modelLabel(static::getResource()::getModelLabel())
            ->form(fn (Form $form): Form => $resource::form($form))
            ->modalWidth('xl');

        if ($action instanceof CreateAction) {
            $action->relationship(($tenant = Filament::getTenant()) ? fn (): Relation => static::getResource()::getTenantRelationship($tenant) : null);
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
