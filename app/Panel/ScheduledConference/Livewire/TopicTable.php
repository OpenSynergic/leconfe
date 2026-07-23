<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Actions\Topics\TopicCreateAction;
use App\Actions\Topics\TopicUpdateAction;
use App\Models\Topic;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class TopicTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithTable;

    public function render()
    {
        return view('tables.table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => Topic::query()->orderBy('order_column'))
            ->heading(__('general.topic'))
            ->reorderable('order_column')
            ->columns([
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make('createtopic')
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn(Schema $schema) => $this->form($schema))
                    ->using(fn(array $data) => TopicCreateAction::run($data)),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn(Schema $schema) => $this->form($schema))
                    ->action(fn(Topic $record, array $data) => TopicUpdateAction::run($record, $data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->required(),
            ]);
    }
}
