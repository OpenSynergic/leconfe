<?php

namespace App\Panel\Livewire\Forms\Blocks;

use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Classes\Block as BlockUtility;
use App\Facades\Block;
use App\Forms\Components\Block as BlockComponent;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class BlockSettingForm extends \Livewire\Component implements HasForms
{
    use InteractsWithForms;

    public array $blocks = [];

    public function mount()
    {
        $this->form->fill([
            'blocks' => [
                'left' => Block::getBlocks(position: 'left', includeInactive: true)
                    ->map(
                        fn (BlockUtility $block) => (object) $block->getSettings()
                    )
                    ->keyBy(
                        fn () => str()->uuid()->toString()
                    ),
                'right' => Block::getBlocks(position: 'right', includeInactive: true)
                    ->map(
                        fn (BlockUtility $block) => (object) $block->getSettings()
                    )
                    ->keyBy(
                        fn () => str()->uuid()->toString()
                    ),
            ]
        ]);
    }

    public function toggleStatus($statePath, $blockSetting, $uuid)
    {
    }

    public function updateBlocks($statePath, $blockSettings)
    {
        $blocks = [];
        foreach ($blockSettings as $sort => $blockSetting) {
            $sort++; // To sort a number, take it from the array index.
            list($uuid, $enabled, $originalState) = explode(':', $blockSetting);
            $block = data_get($this, $originalState . '.' . $uuid);
            // The block is being moved to a new position.
            if ($originalState != $statePath) {
                $block->position = $statePath == 'blocks.left' ? 'left' : 'right';
                $block->sort = $sort;
            }
            $block->active = $enabled == 'enabled';
            $blocks[$uuid] = $block;
        }

        data_set($this, $statePath, $blocks);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                BlockComponent::make('blocks.left')
                    ->label(__("Left Sidebar")),
                BlockComponent::make('blocks.right')
                    ->label(__("Right Sidebar")),
            ]);
    }

    public function submit()
    {
        foreach ($this->blocks as $blocks) {
            foreach ($blocks as $block) {
                UpdateBlockSettingsAction::run($block->class, [
                    'position' => $block->position,
                    'sort' => $block->sort,
                    'active' => $block->active
                ]);
            }
        }

        Notification::make()
            ->title(__('Success'))
            ->body(__('Block settings updated'))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.livewire.forms.blocks.block-setting-form');
    }
}
