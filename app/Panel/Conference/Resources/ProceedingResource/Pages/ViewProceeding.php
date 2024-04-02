<?php

namespace App\Panel\Conference\Resources\ProceedingResource\Pages;

use App\Models\Submission;
use App\Panel\Conference\Resources\ProceedingResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ViewProceeding extends Page implements HasForms, HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProceedingResource::class;

    protected static string $view = 'panel.conference.resources.proceeding-resource.pages.view-proceeding';

    public ?array $data = null;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->form->fill($this->record->attributesToArray());
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function can(string $action, Model | null $record = null)
    {
        return static::getResource()::can($action, $record);
    }

    public function getBreadcrumb(): string
    {
        return __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    public function form(Form $form): Form
    {
        $form
            ->disabled(fn() => !$this->can('update', $this->record))
            ->model($this->record);

        return static::getResource()::form($form)
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::query()
                    ->where('proceeding_id', $this->record->id)
                    ->ordered()
            )
            ->reorderable('proceeding_order_column')
            ->columns([
                TextColumn::make('title')
                    ->getStateUsing(fn (Submission $record) => $record->getMeta('title'))
                    ->url(fn (Submission $record) => route('filament.conference.resources.submissions.view', ['record' => $record->id]))
                    ->searchable()
                    ->color('primary'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('remove')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (Submission $record) => $record->unassignProceeding())
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
