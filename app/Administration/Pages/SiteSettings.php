<?php

namespace App\Administration\Pages;

use App\Actions\Site\SiteUpdateAction;
use App\Administration\Livewire\AccessSetting;
use App\Administration\Livewire\DateAndTimeSetting;
use App\Administration\Livewire\EmailSetting;
use App\Infolists\Components\BladeEntry;
use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
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

class SiteSettings extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-m-cog';

    protected static string $view = 'administration.pages.site-settings';

    public array $informationFormData = [];

    public array $appearanceFormData = [];

    public function mount()
    {
        $this->informationForm->fill([
            'meta' => app()->getSite()->getAllMeta()->toArray(),
        ]);

        $this->appearanceForm->fill([
            //
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('site_settings')
                    ->tabs([
                        Tabs\Tab::make('About')
                            ->schema([
                                VerticalTabs\Tabs::make()
                                    ->tabs([
                                        VerticalTabs\Tab::make('Information')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                BladeEntry::make('general')
                                                    ->blade('{{ $this->informationForm }}'),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('System')
                            ->schema([
                                // BladeEntry::make('general')
                                //     ->blade('{{ $this->systemForm }}'),
                                VerticalTabs\Tabs::make()
                                    ->tabs([
                                        VerticalTabs\Tab::make('Access Options')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                LivewireEntry::make('access_setting')
                                                    ->livewire(AccessSetting::class)
                                                    ->lazy(),
                                            ]),
                                        VerticalTabs\Tab::make('Date & Time')
                                            ->icon('heroicon-o-clock')
                                            ->schema([
                                                LivewireEntry::make('date_and_time')
                                                    ->livewire(DateAndTimeSetting::class)
                                                    ->lazy(),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('E-Mail')
                            ->schema([
                                LivewireEntry::make('mail_setting')
                                    ->livewire(EmailSetting::class)
                                    ->lazy(),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'informationForm',
            'appearanceForm',
        ];
    }

    public function appearanceForm(Form $form): Form
    {
        return $form
            ->statePath('appearanceFormData')
            ->schema([]);
    }

    public function informationForm(Form $form): Form
    {
        return $form
            ->statePath('informationFormData')
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('meta.name')
                            ->label('Website Name')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->model(app()->getSite())
                            ->imageResizeUpscale(false)
                            ->conversion('thumb')
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        Actions::make([
                            Action::make('save')
                                ->successNotificationTitle('Saved!')
                                ->failureNotificationTitle('Failed!')
                                ->action(function (Action $action) {
                                    $data = $this->informationForm->getState();
                                    try {
                                        SiteUpdateAction::run($data);

                                        $action->sendSuccessNotification();
                                    } catch (\Throwable $th) {
                                        $action->sendFailureNotification();
                                    }
                                }),
                        ]),
                    ])
                    ->columns(2),
            ]);
    }
}
