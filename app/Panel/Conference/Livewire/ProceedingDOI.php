<?php

namespace App\Panel\Conference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Utilities\Set;
use App\Classes\DOIGenerator;
use App\Models\Enums\DOIStatus;
use App\Models\Proceeding;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;

class ProceedingDOI extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function mount() {}

    public function table(Table $table): Table
    {
        return $table
            ->query(Proceeding::query()->with('doi'))
            ->columns([
                IndexColumn::make('no'),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('doi.doi')
                    ->searchable()
                    ->label('DOI'),
                TextColumn::make('doi.status')
                    ->badge()
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(DOIStatus::options())
                    ->attribute('doi.status')
                    ->modifyQueryUsing(function ($data, $query) {
                        return ! $data['value'] ? $query : $query->whereHas('doi', fn ($query) => $query->where('status', $data['value']));
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->button()
                    ->fillForm(function (Proceeding $record, Table $table) {
                        return [
                            'doi' => $record->doi?->doi,
                        ];
                    })
                    ->modalWidth(Width::ExtraLarge)
                    ->modalHeading(fn ($record) => $record->title)
                    ->schema([
                        TextInput::make('doi')
                            ->label('DOI')
                            ->suffixAction(
                                Action::make('generate')
                                    ->label('Generate')
                                    ->button()
                                    // ->outlined()
                                    // ->color('secondary')
                                    ->action(fn (Set $set) => $set('doi', DOIGenerator::generate()))
                            ),
                    ])
                    ->action(fn (Proceeding $record, array $data) => $record->doi()->updateOrCreate(['id' => $record->doi?->id], ['doi' => $data['doi']])),
            ])
            ->toolbarActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('panel.conference.livewire.proceeding-doi');
    }
}
