<?php

namespace App\Livewire;

use App\Models\Topic;
use App\Schemas\TopicSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class TopicTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;



    public function render(): View
    {
        return view('livewire.topic-table');
    }

    public function table(Table $table): Table
    {
        return TopicSchema::table($table);
    }


}
