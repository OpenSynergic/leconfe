<?php

namespace App\Livewire\Panel\Tables;

use Filament\Tables;
use App\Models\Topic;
use Livewire\Component;
use Filament\Tables\Table;
use App\Schemas\TopicSchema;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class TopicTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;



    public function render(): View
    {
        return view('livewire.panel.tables.topic-table');
    }

    public function table(Table $table): Table
    {
        return TopicSchema::table($table);
    }
}
