<?php

namespace App\Panel\Pages\Settings;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Illuminate\Validation\Rule;
use Filament\Infolists\Infolist;
use App\Forms\Components\BlockList;
use Filament\Forms\Components\Grid;
use App\Models\Enums\SidebarPosition;
use App\Facades\Block as FacadesBlock;
use App\Forms\Components\VerticalTabs;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Tabs;
use App\Infolists\Components\BladeEntry;
use Filament\Forms\Components\TextInput;
use App\Livewire\Block as BlockComponent;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Actions\Conferences\ConferenceUpdateAction;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Filament\Forms\Components\Section as FormSection;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class Conference extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    public array $generalFormData = [];

    public array $setupFormData = [];

    public array $contactFormData = [];

    public array $appereanceFormData = [];

    public function mount()
    {
        $this->appereanceForm->fill([
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
        ]);
        $this->generalForm->fill([
            ...Filament::getTenant()->attributesToArray(),
            'meta' => Filament::getTenant()->getAllMeta()->toArray(),
        ]);

        $this->contactForm->fill([
            ...Filament::getTenant()->attributesToArray(),
            'meta' => Filament::getTenant()->getAllMeta()->toArray()
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
                        Tabs\Tab::make('About')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->generalForm }}'),
                            ]),
                        Tabs\Tab::make('Contact')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->contactForm }}'),
                            ]),
                        Tabs\Tab::make('Appearance')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->appereanceForm }}'),
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
            'appereanceForm',
            'contactForm',
        ];
    }

    public function updateBlocks($statePath, $blockSettings)
    {
        $blocks = [];
        foreach ($blockSettings as $sort => $blockSetting) {
            $sort++; // To sort a number, take it from the array index.
            [$uuid, $enabled, $originalState] = explode(':', $blockSetting);
            $block = data_get($this, $originalState . '.' . $uuid);
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

    public function appereanceForm(Form $form): Form
    {
        return $form
            ->statePath('appereanceFormData')
            ->schema([
                VerticalTabs\Tabs::make('Sidebar')
                    ->schema([
                        VerticalTabs\Tab::make('Sidebar')
                            ->icon('heroicon-o-view-columns')
                            ->schema([
                                FormSection::make()
                                    ->schema([
                                        CheckboxList::make('sidebar.position')
                                            ->options([
                                                SidebarPosition::Left->getValue() => SidebarPosition::Left->getLabel(),
                                                SidebarPosition::Right->getValue() => SidebarPosition::Right->getLabel(),
                                            ])
                                            ->descriptions([
                                                SidebarPosition::Left->getValue() => SidebarPosition::Left->getLabel() . ' Sidebar',
                                                SidebarPosition::Right->getValue() => SidebarPosition::Right->getLabel() . ' Sidebar',
                                            ])
                                            ->reactive()
                                            ->helperText(__('If you choose both sidebars, the layout will have three columns.')),
                                        Grid::make(3)
                                            ->columns([
                                                'xl' => 3,
                                                'sm' => 3,
                                            ])
                                            ->schema([
                                                BlockList::make('sidebar.blocks.left')
                                                    ->label(__('Left Sidebar'))
                                                    ->reactive(),
                                                BlockList::make('sidebar.blocks.right')
                                                    ->label(__('Right Sidebar'))
                                                    ->reactive(),
                                            ]),
                                        Actions::make([
                                            Action::make('save_appreance')
                                                ->label('Save')
                                                ->successNotificationTitle('Saved!')
                                                ->failureNotificationTitle('Data could not be saved.')
                                                ->action(function (Action $action) {
                                                    try {
                                                        $formData = $this->appereanceForm->getState();
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
                                        ])->alignLeft(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function generalForm(Form $form): Form
    {
        return $form
            ->statePath('generalFormData')
            ->model(Filament::getTenant())
            ->schema([
                VerticalTabs\Tabs::make()
                    ->sticky()
                    ->schema([
                        VerticalTabs\Tab::make('Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                FormSection::make('Information')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                TextInput::make('path')
                                                    ->rule('alpha_dash')
                                                    ->required()
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                TextInput::make('meta.location')
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                Flatpickr::make('meta.date_held')
                                                    ->rule('date')
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                SpatieMediaLibraryFileUpload::make('logo')
                                                    ->collection('logo')
                                                    ->image()
                                                    ->imageResizeUpscale(false)
                                                    ->conversion('thumb')
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                                    ->collection('thumbnail')
                                                    ->helperText('A image representation of the conference that can be used in lists of conferences.')
                                                    ->image()
                                                    ->conversion('thumb')
                                                    ->columnSpan([
                                                        'xl' => 1,
                                                        'sm' => 2,
                                                    ]),
                                                TinyEditor::make('meta.description')
                                                    ->minHeight(300)
                                                    ->columnSpan([
                                                        'sm' => 2,
                                                    ]),
                                                Repeater::make('meta.additional_content')
                                                    ->columnSpanFull()
                                                    ->schema([
                                                        TinyEditor::make('additional')
                                                            ->label('')
                                                            ->minHeight(300)
                                                            ->columnSpan([
                                                                'sm' => 2
                                                            ])
                                                    ])
                                                    ->label('Additional Content')
                                                    ->addActionLabel('New Content'),
                                                TinyEditor::make('meta.about')
                                                    ->label('About Conference')
                                                    ->minHeight(300)
                                                    ->columnSpan([
                                                        'sm' => 2,
                                                    ]),
                                                TinyEditor::make('meta.page_footer')
                                                    ->minHeight(300)
                                                    ->columnSpan([
                                                        'sm' => 2,
                                                    ]),
                                            ]),
                                        Actions::make([
                                            Action::make('save')
                                                ->successNotificationTitle('Saved!')
                                                ->failureNotificationTitle('Data could not be saved.')
                                                ->action(function (Action $action) {
                                                    $formData = $this->generalForm->getState();
                                                    ConferenceUpdateAction::run(Filament::getTenant(), $formData);
                                                    $action->sendSuccessNotification();
                                                }),
                                        ])->alignLeft(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function contactForm(Form $form): Form
    {
        return $form
            ->statePath('contactFormData')
            ->model(Filament::getTenant())
            ->schema([
                VerticalTabs\Tabs::make()
                    ->sticky()
                    ->schema([
                        VerticalTabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                FormSection::make('Contact')
                                    ->schema([
                                        Section::make('')
                                            ->schema([
                                                TextInput::make('meta.email')
                                                    ->email()
                                                    ->required()
                                                    ->helperText(__('Primary contact email for the contact information')),
                                                TextInput::make('meta.address')
                                                    ->helperText(__('The physical location associated with the your company or organization')),
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'xl' => 2
                                            ]),
                                        Section::make('')
                                            ->schema([
                                                // PhoneInput::make('meta.phone')
                                                //     ->helperText(__('Phone will be displayed on the contact information page of the conference website'))
                                                //     ->validateFor(
                                                //         lenient: true,
                                                //     )
                                                //     ->displayNumberFormat(PhoneInputNumberType::E164),

                                                TextInput::make('meta.phone')
                                                    ->rules([
                                                        Rule::phone()
                                                    ])
                                                    ->placeholder('+62 21 1234 5678')
                                                    ->helperText(__('Enter an international phone number along with the country code')),
                                                TextInput::make('meta.bussines_hour')
                                                    ->label(__('Bussines Hour'))
                                                    ->placeholder(__('Mon-Fri from 8am to 5pm'))
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'xl' => 2
                                            ]),
                                        Section::make('')
                                            ->schema([
                                                // PhoneInput::make('meta.whatsapp')
                                                //     ->helperText(__('Automaticly generate a clickable link to your whatsapp'))
                                                //     ->validateFor()
                                                //     ->displayNumberFormat(PhoneInputNumberType::E164),
                                                TextInput::make('meta.whatsapp')
                                                    ->rules([
                                                        Rule::phone()
                                                    ])
                                                    ->placeholder('+62 21 1234 5678')
                                                    ->helperText(__('Automaticly generate a clickable link to your whatsapp')),
                                                TextInput::make('meta.label_chat')
                                                    ->label(__('Chat label'))
                                                    ->placeholder('Start new chat')
                                                    ->helperText(__('This will be the clickable title of the link.'))
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'xl' => 2
                                            ]),
                                        Actions::make([
                                            Action::make('Save')
                                                ->successNotificationTitle('Saved!')
                                                ->failureNotificationTitle('Data could not be saved')
                                                ->action(function (Action $action) {
                                                    $contactData = $this->contactForm->getState();
                                                    ConferenceUpdateAction::run(Filament::getTenant(), $contactData);
                                                    $action->sendSuccessNotification();
                                                })
                                        ])
                                    ])
                            ])
                    ])
            ]);
    }

    public function setupForm(Form $form): Form
    {
        return $form
            ->statePath('setupFormData')
            ->model(Filament::getTenant())
            ->schema([
                VerticalTabs\Tabs::make()
                    ->tabs(
                        [
                            VerticalTabs\Tab::make('Privacy Statement')
                                ->icon('heroicon-m-window')
                                ->schema(
                                    [
                                        FormSection::make('Privacy Statement')
                                            ->schema([
                                                TinyEditor::make('meta.privacy_statement')->label('Privacy Statement'),
                                                Actions::make([
                                                    Action::make('setup_save')
                                                        ->label('Save')
                                                        ->successNotificationTitle('Saved!')
                                                        ->action(function (Action $action) {
                                                            $setupFormData = $this->setupForm->getState();
                                                            ConferenceUpdateAction::run(Filament::getTenant(), $setupFormData);
                                                            $action->sendSuccessNotification();
                                                        }),
                                                ])->alignLeft(),
                                            ])
                                            ->extraAttributes([
                                                'class' => '!p-0',
                                            ]),
                                    ]
                                ),
                        ]
                    ),
            ]);
    }
}
