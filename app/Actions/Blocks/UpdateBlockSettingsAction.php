<?php

namespace App\Actions\Blocks;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Block as BlockSetting;

class UpdateBlockSettingsAction
{
    use AsAction;

    public function handle(string $blockName, array $settings)
    {
        BlockSetting::updateOrCreate([
            'class' => $blockName,
        ], $settings);
    }
}
