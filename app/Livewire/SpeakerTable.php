<?php

namespace App\Livewire;

use App\Models\Speaker;
use App\Schemas\SpeakerSchema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class SpeakerTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;


    public function render(): View
    {
        return view('livewire.speaker-table');
    }

    public function table(Table $table): Table
    {
        return SpeakerSchema::table($table);
    }
}
