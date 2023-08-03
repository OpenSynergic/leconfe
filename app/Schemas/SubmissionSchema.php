<?php

namespace App\Schemas;

use App\Models\Submission;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SubmissionSchema
{
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('title')
          ->getStateUsing(fn (Submission $record) => $record->getMeta('title'))
          ->searchable(query: function (Builder $query, string $search): Builder {
            return $query
              ->whereMeta('title', 'like', "%{$search}%");
          })
      ]);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema(static::formSchemas());
  }

  public static function formSchemas(): array
  {
    return [];
  }
}
