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
use App\Models\SubmissionFileType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class SubmissionFileTypeTable extends Component implements HasForms, HasTable, HasActions
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
            ->query(SubmissionFileType::withCount(['files']))
            ->defaultPaginationPageOption(10)
            ->heading(__('general.file_types'))
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->searchable(),
                IconColumn::make('required')
                    ->label(__('general.required'))
                    ->boolean(),
                TextColumn::make('files_count')
                    ->label(__('general.files')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('general.add_a_component'))
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema)),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema)),
                DeleteAction::make()
                    ->hidden(fn (SubmissionFileType $record) => $record->files_count > 0),
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
                Checkbox::make('required')
                    ->label(__('general.required_upload'))
                    ->helperText(__('general.required_upload_helper')),
            ]);
    }
}
