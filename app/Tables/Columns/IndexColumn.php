<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class IndexColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extraCellAttributes([
            'style' => 'width: 1px',
        ]);

        $this->grow(false);

        $this->rowIndex();
    }
}
