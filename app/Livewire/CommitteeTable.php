<?php

namespace App\Livewire;

use App\Schemas\CommitteeSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CommitteeTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.committee-table');
    }

    public function table(Table $table): Table
    {
        return CommitteeSchema::table($table);
    }
}
