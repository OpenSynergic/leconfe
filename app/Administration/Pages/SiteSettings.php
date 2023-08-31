<?php

namespace App\Administration\Pages;

use App\Actions\Settings\SettingUpdateAction;
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

class SiteSettings extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-m-cog';

    protected static string $view = 'administration.pages.site-settings';

    public array $setupFormData = [];

    public function mount()
    {
        $this->setupForm->fill([
            'format' => setting('format'),
            'privacy_statement' => setting('privacy_statement'),
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('site_settings')
                    ->tabs([
                        Tabs\Tab::make('Setup')
                            ->icon('heroicon-m-window')
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
            'setupForm',
        ];
    }

    public function setupForm(Form $form): Form
    {
        $now = now()->hours(16);

        return $form
            ->statePath('setupFormData')
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
                                SettingUpdateAction::run($this->setupForm->getState());

                                $action->sendSuccessNotification();
                            } catch (\Throwable $th) {
                                $action->sendFailureNotification();
                            }
                        }),
                ])->alignRight(),
            ]);
    }
}
