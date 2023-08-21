<?php

namespace App\Livewire;

use App\Schemas\CommitteeMemberSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CommitteeMemberTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.committee-member-table');
    }

    public function table(Table $table): Table
    {
        return CommitteeMemberSchema::table($table);
    }
}
