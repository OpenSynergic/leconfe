<?php

namespace App\Administration\Pages;

use App\Actions\Settings\SettingUpdateAction;
use App\Actions\Site\SiteUpdateAction;
use App\Infolists\Components\BladeEntry;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use App\Infolists\Components\VerticalTabs;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;

class SiteSettings extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-m-cog';

    protected static string $view = 'administration.pages.site-settings';

    public array $systemFormData = [];

    public array $informationFormData = [];

    public array $appearanceFormData = [];

    public function mount()
    {
        $this->systemForm->fill([
            'format' => setting('format'),
            'privacy_statement' => setting('privacy_statement'),
        ]);

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
                                        VerticalTabs\Tab::make("Information")
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                BladeEntry::make('general')
                                                    ->blade('{{ $this->informationForm }}'),
                                            ])
                                    ]),
                            ]),
                        Tabs\Tab::make('System')
                            ->schema([
                                BladeEntry::make('general')
                                    ->blade('{{ $this->systemForm }}'),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'informationForm',
            'systemForm',
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
                    ->columns(2)
            ]);
    }

    public function systemForm(Form $form): Form
    {
        $now = now()->hours(16);

        return $form
            ->statePath('systemFormData')
            ->schema([
                Section::make('Date and Time Formats')
                    ->description(new HtmlString(<<<'HTML'
                                        Please select the desired format for dates and times. You may also enter a custom format using
                                    special <a href="https://www.php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters" target="_blank"
                                        class="filament-link inline-flex items-center justify-center gap-0.5 font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 filament-tables-link-action">format characters</a>.
                                    HTML))
                    ->schema([
                        Radio::make('format.date')
                            ->options(fn () => collect([
                                'F j, Y',
                                'F j Y',
                                'j F Y',
                                'Y F j',
                            ])->mapWithKeys(fn ($format) => [$format => $now->format($format)])),
                        Radio::make('format.time')
                            ->options(fn () => collect([
                                'h:i A',
                                'g:ia',
                                'H:i',
                            ])->mapWithKeys(fn ($format) => [$format => $now->format($format)])),
                    ])
                    ->aside(),
                Actions::make([
                    Action::make('save')
                        ->successNotificationTitle('Saved!')
                        ->action(function (Action $action) {
                            try {
                                SettingUpdateAction::run($this->systemForm->getState());

                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignRight(),
            ]);
    }
}
