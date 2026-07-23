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
use App\Actions\Stakeholders\StakeholderCreateAction;
use App\Actions\Stakeholders\StakeholderUpdateAction;
use App\Models\Stakeholder;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Select;
use App\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class SponsorTable extends Component implements HasForms, HasTable, HasActions
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
            ->query(Stakeholder::sponsors())
            ->heading(__('general.sponsors'))
            ->reorderable('order_column')
            ->defaultSort('order_column', 'asc')
            ->defaultGroup('level.name')
            ->columns([
                IndexColumn::make('no.'),
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label(__('general.logo'))
                    ->collection('logo')
                    ->collection('logo'),
                TextColumn::make('name')
                    ->label(__('general.name'))
                    ->description(fn (Stakeholder $record) => $record->description)
                    ->searchable(),
                TextColumn::make('level.name')
                    ->label(__('general.level'))
                    ->badge()
                    ->searchable(),
                ToggleColumn::make('is_shown')
                    ->label(__('general.shown')),
            ])
            ->emptyStateHeading(__('general.no_sponsors'))
            ->emptyStateDescription(__('general.add_sponsor_to_get_started'))
            ->headerActions([
                CreateAction::make()
                    ->label(__('general.add_sponsor'))
                    ->modalHeading(__('general.create_sponsor'))
                    ->mutateDataUsing(function (array $data): array {
                        $data['type'] = Stakeholder::TYPE_SPONSOR;

                        return $data;
                    })
                    ->modalWidth(Width::ExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema))
                    ->using(fn (array $data) => StakeholderCreateAction::run($data)),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ExtraLarge)
                    ->mutateRecordDataUsing(function (Stakeholder $record, array $data): array {
                        $data['meta']['url'] = $record->getMeta('url');

                        return $data;
                    })
                    ->mutateFormDataUsing(function (Stakeholder $record, array $data): array {
                        $data['type'] = Stakeholder::TYPE_SPONSOR;

                        return $data;
                    })
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->using(fn (Stakeholder $record, array $data) => StakeholderUpdateAction::run($record, $data)),
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
                SpatieMediaLibraryFileUpload::make('logo')
                    ->label(__('general.logo'))
                    ->image()
                    ->key('logo')
                    ->collection('logo')
                    ->alignCenter()
                    ->imageResizeUpscale(false),
                TextInput::make('name')
                    ->label(__('general.name'))
                    ->required(),
                TextInput::make('meta.url')
                    ->label(__('general.url'))
                    ->url()
                    ->validationMessages([
                        'url' => __('general.url_must_be_valid'),
                    ]),
                Select::make('level_id')
                    ->label(__('general.level'))
                    ->relationship('level', 'name', fn ($query) => $query->sponsors()->orderBy('order_column', 'asc')),
            ]);
    }
}
