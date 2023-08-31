<?php

namespace App\Panel\Livewire\Forms\Website;

use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Actions\Settings\SettingUpdateAction;
use App\Classes\Block as BlockUtility;
use App\Facades\Block;
use App\Forms\Components\Block as BlockComponent;
use App\Models\Constants\SidebarPosition;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class SidebarSettingForm extends \Livewire\Component implements HasForms
{
    use InteractsWithForms;

    public array $blocks = [];

    public array $sidebar;

    public function mount()
    {
        $this->form->fill([
            'sidebar' =>  match (setting('sidebar')) {
                SidebarPosition::Left => [SidebarPosition::Left],
                SidebarPosition::Right => [SidebarPosition::Right],
                SidebarPosition::Both => [SidebarPosition::Left, SidebarPosition::Right],
                SidebarPosition::None => [],
                default => [SidebarPosition::Left, SidebarPosition::Right],
            },
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
            }
            $block->sort = $sort;
            $block->active = $enabled == 'enabled';
            $blocks[$uuid] = $block;
        }

        data_set($this, $statePath, $blocks);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CheckboxList::make('sidebar')
                    ->options([
                        SidebarPosition::Left => 'Left',
                        SidebarPosition::Right => 'Right',
                    ])
                    ->descriptions([
                        SidebarPosition::Left => 'Left Sidebar',
                        SidebarPosition::Right => 'Right Sidebar',
                    ])
                    ->reactive()
                    ->helperText(__('If you choose both sidebars, the layout will have three columns.')),
                Grid::make(2)
                    ->schema([
                        BlockComponent::make('blocks.left')
                            ->label(__("Left Sidebar"))
                            ->hidden(fn () => !in_array(SidebarPosition::Left, $this->sidebar))
                            ->reactive(),
                        BlockComponent::make('blocks.right')
                            ->label(__("Right Sidebar"))
                            ->hidden(fn () => !in_array(SidebarPosition::Right, $this->sidebar))
                            ->reactive(),
                    ])

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

        $sidebar = collect($this->sidebar);
        $sidebarPosition = $sidebar->first();

        if ($sidebar->isEmpty()) {
            $sidebarPosition = SidebarPosition::None;
        }

        if ($sidebar->count() >= 2) {
            $sidebarPosition = SidebarPosition::Both;
        }

        SettingUpdateAction::run([
            'sidebar' => str($sidebarPosition)->lower(),
        ]);

        Notification::make()
            ->title(__('Success'))
            ->body(__('Block settings updated'))
            ->success()
            ->send();
    }

    public function render()
    {
        return view('panel.livewire.forms.sidebar.sidebar-setting-form');
    }
}
