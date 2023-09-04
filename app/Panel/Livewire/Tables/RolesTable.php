<?php

namespace App\Panel\Livewire\Tables;

use App\Panel\Livewire\Traits\PlaceholderTrait;
use App\Panel\Resources\RoleResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class RolesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable, PlaceholderTrait;

    protected static string $resource = RoleResource::class;

    public function render()
    {
        return view('panel.livewire.tables.table');
    }

    public function table(Table $table): Table
    {
        return RoleResource::table($table)
            ->query(fn (): Builder => static::getResource()::getEloquentQuery());
    }

    protected function configureTableAction(Tables\Actions\Action $action): void
    {
        match (true) {
            $action instanceof Tables\Actions\EditAction => $this->configureEditAction($action),
            default => null,
        };
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize(fn (Model $record): bool => $resource::canEdit($record))
            ->form(fn (Form $form): Form => $this->form($form->columns(2)));

        if ($resource::hasPage('edit')) {
            $action->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]));
        }
    }

    public static function getResource(): string
    {
        return static::$resource;
    }
}
