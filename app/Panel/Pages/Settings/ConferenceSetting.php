<?php

namespace App\Panel\Pages\Settings;

use App\Actions\Blocks\UpdateBlockSettingsAction;
use App\Actions\Conferences\ConferenceUpdateAction;
use App\Facades\Block as FacadesBlock;
use App\Forms\Components\BlockList;
use App\Forms\Components\VerticalTabs;
use App\Infolists\Components\BladeEntry;
use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs as InfolistsVerticalTabs;
use App\Livewire\Block as BlockComponent;
use App\Panel\Livewire\Forms\Conferences\InformationSetting;
use App\Panel\Livewire\Forms\Conferences\PrivacySetting;
use App\Panel\Livewire\Forms\Conferences\SidebarSetting;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class ConferenceSetting extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static string $view = 'panel.pages.settings.conference';

    protected ?string $heading = 'Conference Settings';

    protected static ?string $navigationLabel = 'Conference';

    public array $generalFormData = [];

    public array $contactFormData = [];

    public function mount()
    {
        $conference = App::getCurrentConference();

        $this->generalForm->fill([
            ...$conference->attributesToArray(),
            'meta' => $conference->getAllMeta()->toArray(),
        ]);

        $this->contactForm->fill([
            'meta' => $conference->getAllMeta()->toArray(),
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
                                InfolistsVerticalTabs\Tabs::make()
                                    ->schema([
                                        InfolistsVerticalTabs\Tab::make('Information')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                LivewireEntry::make('information-setting')
                                                    ->livewire(InformationSetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                        InfolistsVerticalTabs\Tab::make('Privacy')
                                            ->icon('heroicon-o-shield-check')
                                            ->schema([
                                                LivewireEntry::make('information-setting')
                                                    ->livewire(PrivacySetting::class, [
                                                        'conference' => App::getCurrentConference(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Appearance')
                            ->schema([
                                InfolistsVerticalTabs\Tabs::make()
                                ->schema([
                                    InfolistsVerticalTabs\Tab::make('Sidebar')
                                        ->icon('heroicon-o-view-columns')
                                        ->schema([
                                            LivewireEntry::make('sidebar-setting')
                                                ->livewire(SidebarSetting::class, [
                                                    'conference' => App::getCurrentConference(),
                                                ]),
                                        ]),
                                ]),
                            ]),
                        Tabs\Tab::make('Contact')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->contactForm }}'),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'generalForm',
            'contactForm',
        ];
    }

    public function generalForm(Form $form): Form
    {
        return $form
            ->statePath('generalFormData')
            ->model(app()->getCurrentConference())
            ->schema([
                Section::make()
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
                                Textarea::make('meta.description')
                                    ->rows(5)
                                    ->autosize()
                                    ->columnSpanFull(),
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
                            Action::make('save_general_form')
                                ->label('Save')
                                ->successNotificationTitle('Saved!')
                                ->failureNotificationTitle('Data could not be saved.')
                                ->action(function (Action $action) {
                                    $formData = $this->generalForm->getState();
                                    try {
                                        ConferenceUpdateAction::run(app()->getCurrentConference(), $formData);
                                        $action->sendSuccessNotification();
                                    } catch (\Throwable $th) {
                                        $action->sendFailureNotification();
                                    }
                                }),
                        ])->alignLeft(),
                    ]),
            ]);
    }

    public function contactForm(Form $form): Form
    {
        return $form
            ->statePath('contactFormData')
            ->model(app()->getCurrentConference())
            ->schema([
                VerticalTabs\Tabs::make()
                    ->sticky()
                    ->schema([
                        VerticalTabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('Contact')
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
                                                'xl' => 2,
                                            ]),
                                        Section::make('')
                                            ->schema([
                                                TextInput::make('meta.phone')
                                                    ->rules([Rule::phone()->international()])
                                                    ->placeholder('International format, e.g. +6281234567890')
                                                    ->helperText(__('International phone number along with the country code')),
                                                TextInput::make('meta.bussines_hour')
                                                    ->label(__('Bussines Hour'))
                                                    ->placeholder(__('Mon-Fri from 8am to 5pm')),
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'xl' => 2,
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
                                                    ->helperText(__('This will be the clickable title of the link.')),
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'xl' => 2,
                                            ]),
                                        Actions::make([
                                            Action::make('Save')
                                                ->successNotificationTitle('Saved!')
                                                ->failureNotificationTitle('Data could not be saved')
                                                ->action(function (Action $action) {
                                                    $contactData = $this->contactForm->getState();
                                                    try {
                                                        ConferenceUpdateAction::run(app()->getCurrentConference(), $contactData);
                                                        $action->sendSuccessNotification();
                                                    } catch (\Throwable $th) {
                                                        $action->sendFailureNotification();
                                                    }
                                                }),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
