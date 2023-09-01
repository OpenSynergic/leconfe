<?php

namespace App\Panel\Pages\Settings;

use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\Block as FacadesBlock;
use App\Forms\Components\BlockList;
use App\Infolists\Components\BladeEntry;
use App\Livewire\Block as BlockComponent;
use App\Models\Enums\SidebarPosition;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Conference extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    public array $generalFormData = [];

    public array $setupFormData = [];

    public function mount()
    {
        $this->generalForm->fill([
            ...Filament::getTenant()->attributesToArray(),
            'sidebar' => [
                'position' => match (Filament::getTenant()->getMeta('sidebar')) {
                    SidebarPosition::Left->getValue() => [SidebarPosition::Left->getValue()],
                    SidebarPosition::Right->getValue() => [SidebarPosition::Right->getValue()],
                    SidebarPosition::Both->getValue() => [SidebarPosition::Left->getValue(), SidebarPosition::Right->getValue()],
                    SidebarPosition::None->getValue() => [],
                    default => [SidebarPosition::Left->getValue(), SidebarPosition::Right->getValue()],
                },
                'blocks' => [
                    'left' => FacadesBlock::getBlocks(position: 'left', includeInactive: true)
                        ->map(
                            fn (BlockComponent $block) => (object) $block->getSettings()
                        )
                        ->keyBy(
                            fn () => str()->uuid()->toString()
                        ),
                    'right' => FacadesBlock::getBlocks(position: 'right', includeInactive: true)
                        ->map(
                            fn (BlockComponent $block) => (object) $block->getSettings()
                        )
                        ->keyBy(
                            fn () => str()->uuid()->toString()
                        ),
                ],
            ],
            'meta' => Filament::getTenant()->getAllMeta()->toArray(),
        ]);

        $this->setupForm->fill([
            'meta' => Filament::getTenant()->getAllMeta()->toArray(),
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('conference_settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->generalForm }}'),
                            ]),
                        Tabs\Tab::make('Setup')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->setupForm }}'),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'generalForm',
            'setupForm',
        ];
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

    public function generalForm(Form $form): Form
    {
        return $form
            ->statePath('generalFormData')
            ->model(Filament::getTenant())
            ->schema([
                FormSection::make('Information')
                    ->aside()
                    ->columns([
                        'sm' => 2,
                    ])
                    ->description('General Information about the conference.')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('path')
                            ->rule('alpha_dash')
                            ->required(),
                        TextInput::make('meta.location'),
                        Flatpickr::make('meta.date_held'),
                        TinyEditor::make('meta.description')
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        TinyEditor::make('meta.about')
                            ->label('About Conference')
                            ->minHeight(300)
                            ->columnSpan([
                                'sm' => 2,
                            ]),

                    ]),
                FormSection::make('Appearance')
                    ->aside()
                    ->description('Appearance settings for the conference.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->imageResizeUpscale(false)
                            ->imageEditor()
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->collection('thumbnail')
                            ->helperText('A image representation of the conference that can be used in lists of conferences.')
                            ->image()
                            ->conversion('thumb'),
                        TinyEditor::make('meta.page_footer'),
                        CheckboxList::make('sidebar.position')
                            ->options([
                                SidebarPosition::Left->getValue() => SidebarPosition::Left->getLabel(),
                                SidebarPosition::Right->getValue() => SidebarPosition::Right->getLabel(),
                            ])
                            ->descriptions([
                                SidebarPosition::Left->getValue() => SidebarPosition::Left->getLabel().' Sidebar',
                                SidebarPosition::Right->getValue() => SidebarPosition::Right->getLabel().' Sidebar',
                            ])
                            ->reactive()
                            ->helperText(__('If you choose both sidebars, the layout will have three columns.')),
                        Grid::make(2)
                            ->schema([
                                BlockList::make('sidebar.blocks.left')
                                    ->label(__('Left Sidebar'))
                                    ->reactive(),
                                BlockList::make('sidebar.blocks.right')
                                    ->label(__('Right Sidebar'))
                                    ->reactive(),
                            ]),
                    ]),
                Actions::make([
                    Action::make('save')
                        ->successNotificationTitle('Saved!')
                        ->failureNotificationTitle('Data could not be saved.')
                        ->action(function (Action $action) {
                            try {
                                $formData = $this->generalForm->getState();
                                ConferenceUpdateAction::run(Filament::getTenant(), $formData);

                                $sidebarFormData = $formData['sidebar'];

                                foreach ($sidebarFormData['blocks'] as $blocks) {
                                    foreach ($blocks as $block) {
                                        UpdateBlockSettingsAction::run($block->class, [
                                            'position' => $block->position,
                                            'sort' => $block->sort,
                                            'active' => $block->active,
                                        ]);
                                    }
                                }

                                $sidebar = collect($formData['sidebar']['position']);
                                $sidebarPosition = $sidebar->first();

                                if ($sidebar->isEmpty()) {
                                    $sidebarPosition = SidebarPosition::None->getValue();
                                }

                                if ($sidebar->count() >= 2) {
                                    $sidebarPosition = SidebarPosition::Both->getValue();
                                }

                                FIlament::getTenant()
                                    ->setMeta('sidebar', $sidebarPosition);

                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignRight(),
            ]);
    }

    public function setupForm(Form $form): Form
    {
        return $form
            ->statePath('setupFormData')
            ->model(Filament::getTenant())
            ->schema([
                FormSection::make('Privacy Statement')
                    ->description('This statement will be displayed during user registration as well as on the public privacy page. Please note that in certain jurisdictions, there may be legal requirements mandating the disclosure of your data handling practices within this privacy policy.')
                    ->schema([
                        TinyEditor::make('meta.privacy_statement')->label(''),
                    ])
                    ->aside(),
                Actions::make([
                    Action::make('setup_save')
                        ->label('Save')
                        ->successNotificationTitle('Saved!')
                        ->action(function (Action $action) {
                            try {
                                ConferenceUpdateAction::run(Filament::getTenant(), $this->setupForm->getState());

                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignRight(),
            ]);
    }
}
