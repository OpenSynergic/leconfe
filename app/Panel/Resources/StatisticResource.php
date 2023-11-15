<?php

namespace App\Panel\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Statistic;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Panel\Resources\StatisticResource\Pages;
use App\Panel\Resources\StatisticResource\Widgets\ConferenceOverviewWidget;


class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;


    protected static ?string $navigationGroup = 'Settings';


    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';


    public static function getBreadcrumb(): string
    {
        return '';
    }

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->paginated(false)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No statistics yet')
            ->emptyStateActions([]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatistics::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ConferenceOverviewWidget::class,
        ];
    }
}
