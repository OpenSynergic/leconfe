<?php

namespace App\Actions\Blocks;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Block;
use Illuminate\Support\Facades\DB;

class UpdateBlockSettingsAction
{
    use AsAction;

    public function handle(string $blockName, array $settings) : Block
    {
        try {
            DB::beginTransaction();

            $block = Block::updateOrCreate([
                'class' => $blockName,
            ], $settings);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $block;
    }
}
