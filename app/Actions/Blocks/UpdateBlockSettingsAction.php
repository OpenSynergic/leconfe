<?php

namespace App\Actions\Blocks;

use App\Models\Block;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBlockSettingsAction
{
    use AsAction;

    public function handle(string $blockName, array $settings): Block
    {
        try {
            DB::beginTransaction();

            $block = Block::updateOrCreate([
                'name' => $blockName,
            ], $settings);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return $block;
    }
}
