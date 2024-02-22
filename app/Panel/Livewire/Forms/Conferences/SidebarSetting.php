<?php

namespace App\Panel\Livewire\Forms\Conferences;

use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Facades\Block as FacadesBlock;
use App\Forms\Components\BlockList;
use App\Livewire\Block as BlockComponent;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class SidebarSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public Conference $conference;

    public ?array $formData = [];

    public function mount(Conference $conference): void
    {
        $this->form->fill([
            'sidebar' => [
                'blocks' => [
                    'left' => FacadesBlock::getBlocks(position: 'left', includeInactive: true)
                        ->mapWithKeys(
                            fn (BlockComponent $block) => [$block->getDatabaseName() => (object) $block->getSettings()]
                        ),
                    'right' => FacadesBlock::getBlocks(position: 'right', includeInactive: true)
                        ->mapWithKeys(
                            fn (BlockComponent $block) => [$block->getDatabaseName() => (object) $block->getSettings()]
                        ),
                ],
            ],
        ]);
    }

    public function render()
    {
        return view('panel.livewire.form');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->conference)
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->columns([
                                'sm' => 2,
                            ])
                            ->schema([
                                BlockList::make('sidebar.blocks.left')
                                    ->label(__('Left Sidebar')),
                                BlockList::make('sidebar.blocks.right')
                                    ->label(__('Right Sidebar')),
                            ]),

                    ]),
                Actions::make([
                    Action::make('save')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            $formData = $this->form->getState();
                            try {
                                $sidebarFormData = $formData['sidebar'];
                                foreach ($sidebarFormData['blocks'] as $blocks) {
                                    foreach ($blocks as $block) {
                                        UpdateBlockSettingsAction::run($block->database_name, [
                                            'position' => $block->position,
                                            'sort' => $block->sort,
                                            'active' => $block->active,
                                        ]);
                                    }
                                }
                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                                throw $th;
                            }
                        }),
                ])->alignLeft(),
            ])
            ->statePath('formData');
    }

    public function updateBlocks($statePath, $blockSettings)
    {
        $blocks = [];
        foreach ($blockSettings as $sort => $blockSetting) {
            $sort++; // To sort a number, take it from the array index.
            [$uuid, $enabled, $originalState] = explode(':', $blockSetting);
            $block = data_get($this, $originalState.'.'.$uuid);
            // The block is being moved to a new position.
            if ($originalState != $statePath) {
                $block->position = str($statePath)->contains('blocks.left') ? 'left' : 'right';
            }

            $block->sort = $sort;
            $block->active = $enabled == 'enabled';
            $blocks[$uuid] = $block;
        }

        data_set($this, $statePath, $blocks);
    }
}
