<?php

namespace App\Schemas;

use App\Models\Role;
use App\Models\Timeline;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Enums\UserRole;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;

class TimelineSchema
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(Timeline::query()->orderBy('date'))
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('date')
                    ->dateTime('d M Y'),
                TextColumn::make('roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Author' => 'warning',
                        'Reviewer' => 'gray',
                        'Participant' => 'primary',
                        'Editor' => 'gray',
                        default => 'primary'
                    })
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        // costumize date format before filling the form
                        ->mutateRecordDataUsing(function (array $data): array {
                            $dateFormat = date('d M Y', strtotime($data['date']));
                            $data['date'] = $dateFormat;
                            return $data;
                        })
                        // costumize date format before saving to database
                        ->mutateFormDataUsing(function (array $data): array {
                            $dateFormat = date('Y-m-d', strtotime($data['date']));
                            $data['date'] = $dateFormat;
                            return $data;
                        }),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formSchemas());
    }

    public static function formSchemas(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('subtitle'),
                    Flatpickr::make('date')
                        ->dateFormat('d M Y')
                        ->rule('date')
                        ->required(),
                    Select::make('roles')
                        ->options(Role::all()->pluck('name', 'name'))
                        ->multiple()
                ]),

        ];
    }
}
