<?php

namespace App\Panel\Administration\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Actions\Stakeholders\StakeholderLevelCreateAction;
use App\Actions\Stakeholders\StakeholderLevelUpdateAction;
use App\Models\StakeholderLevel;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class SponsorLevelTable extends Component implements HasForms, HasTable, HasActions
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
            ->query(StakeholderLevel::sponsors())
            ->heading(__('general.sponsorship_levels'))
            ->reorderable('order_column')
            ->defaultSort('order_column', 'asc')
            ->columns([
                IndexColumn::make('no.'),
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->description(fn (StakeholderLevel $record) => $record->description)
                    ->searchable(),
                ToggleColumn::make('is_shown')
                    ->label(__('general.shown')),
            ])

            ->headerActions([
                CreateAction::make()
                    ->label(__('general.add_sponsorship_level'))
                    ->modalHeading(__('general.create_sponsorship_level'))
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema))
                    ->using(fn (array $data) => StakeholderLevelCreateAction::run($data)),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema))
                    ->action(fn (StakeholderLevel $record, array $data) => StakeholderLevelUpdateAction::run($record, $data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(StakeholderLevel::class)
            ->schema([
                Hidden::make('type')
                    ->default(StakeholderLevel::TYPE_SPONSOR),
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('general.description')),
            ]);
    }
}
