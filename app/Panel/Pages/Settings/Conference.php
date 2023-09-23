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
            'contactForm',
        ];
    }


    public function generalForm(Form $form): Form
    {
        return $form
            ->statePath('generalFormData')
            ->model(app()->getCurrentConference())
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
                                                    ConferenceUpdateAction::run(app()->getCurrentConference(), $formData);
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
                                                    ->placeholder('example@gmail.com')
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
                                                TextInput::make('meta.phone')
                                                    ->rules([Rule::phone()->international()])
                                                    ->placeholder('International format, e.g. +6281234567890')
                                                    ->helperText(__('International phone number along with the country code')),
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
                                                TextInput::make('meta.whatsapp')
                                                    ->rules([Rule::phone()->international()])
                                                    ->placeholder('International format, e.g. +6281234567890')
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
                                                    ConferenceUpdateAction::run(app()->getCurrentConference(), $contactData);
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
            ->model(app()->getCurrentConference())
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
                                                            ConferenceUpdateAction::run(app()->getCurrentConference(), $setupFormData);
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