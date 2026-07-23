<?php

namespace App\Panel\ScheduledConference\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Throwable;
use Filament\Schemas\Components\Grid;
use App\Actions\Tracks\TrackCreateAction;
use App\Actions\Tracks\TrackUpdateAction;
use App\Forms\Components\TinyEditor;
use App\Models\Enums\UserRole;
use App\Models\Track;
use App\Models\User;
use App\Tables\Columns\IndexColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class TrackTable extends Component implements HasForms, HasTable, HasActions
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
            ->deferLoading()
            ->query(Track::with(['meta'])->orderBy('order_column'))
            ->heading(__('general.track'))
            ->columns([
                IndexColumn::make('no')
                    ->label('No.'),
                TextColumn::make('title')
                    ->label(__('general.title')),
                TextColumn::make('editors')
                    ->wrap()
                    ->bulleted()
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) {
                        return ! empty($record->getMeta('track_editors')) ? User::with(['meta'])->role(UserRole::TrackEditor)->whereIn('id', $record->getMeta('track_editors'))->get()->pluck('fullName', 'id') : [];
                    }),
            ])
            ->reorderable('order_column')
            ->headerActions([
                CreateAction::make()
                    ->label(__('general.new_track'))
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema))
                    ->using(fn (array $data) => TrackCreateAction::run($data)),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->schema(fn (Schema $schema) => $this->form($schema))
                    ->mutateRecordDataUsing(function (array $data, Track $record) {
                        $data['meta'] = $record->getAllMeta();

                        return $data;
                    })
                    ->action(fn (Track $record, array $data) => TrackUpdateAction::run($record, $data)),
                DeleteAction::make()
                    ->using(function (Track $record, DeleteAction $action) {
                        try {
                            $record->delete();

                        } catch (Throwable $th) {

                            $action->failureNotificationTitle($th->getMessage());

                            return false;
                        }

                        return true;
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('general.track_title'))
                            ->required(),
                        TextInput::make('abbreviation')
                            ->label(__('general.abbreviation'))
                            ->alpha()
                            ->required(),
                    ]),
                TinyEditor::make('meta.policy')
                    ->label(__('general.track_policy'))
                    ->profile('basic'),
                TextInput::make('meta.abstract_word_count')
                    ->label(__('general.track_abstract_word_count'))
                    ->helperText(__('general.track_abstract_word_count_helper'))
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0),
                Checkbox::make('meta.do_not_require_abstracts')
                    ->label(__('general.track_do_not_require_abstracts')),
                Checkbox::make('meta.submit_only_for_editors')
                    ->label(__('general.track_submit_only_for_editors')),
                Checkbox::make('meta.hide_author')
                    ->label(__('general.track_hide_author')),
                CheckboxList::make('meta.track_editors')
                    ->label(__('general.track_editors'))
                    ->options(fn () => User::with(['meta'])->role(UserRole::TrackEditor)->get()->pluck('fullName', 'id')),
            ]);
    }
}
