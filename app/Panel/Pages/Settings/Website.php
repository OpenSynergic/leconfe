<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Panel\Livewire\Forms\DateTimeSettingForm;
use App\Panel\Livewire\Forms\PrivacyStatementForm;
use App\Panel\Livewire\Forms\Website\GeneralSettingForm;
use App\Panel\Livewire\Tables\StaticPageTable;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class Website extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    protected static string $view = 'panel.pages.settings.website';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Website Settings')
                    ->tabs([
                        Tabs\Tab::make('Appearance')
                            ->schema([
                                // Tabs::make('Label')
                                //     ->tabs([
                                //         Tabs\Tab::make('General')
                                //             ->schema([
                                Section::make('General')
                                    ->description('General Information about the conference.')
                                    ->schema([
                                        LivewireEntry::make('GeneralSettingForm', GeneralSettingForm::class, [
                                            'record' => Filament::getTenant(),
                                        ]),
                                    ])
                                    ->aside(),
                                //         ]),
                                // ])
                                // ->contained(),
                            ]),
                        Tabs\Tab::make('Setup')
                            ->schema([
                                Section::make('Date and Time Formats')
                                    ->description(new HtmlString(<<<'HTML'
                                        Please select the desired format for dates and times. You may also enter a custom format using
                                    special <a href="https://www.php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters" target="_blank"
                                        class="filament-link inline-flex items-center justify-center gap-0.5 font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 filament-tables-link-action">format characters</a>.
                                    HTML))
                                    ->schema([
                                        LivewireEntry::make('datetimesetting', DateTimeSettingForm::class)
                                            ->lazy(),
                                    ])
                                    ->aside(),
                                Section::make('Privacy Statement')
                                    ->description('This statement will be displayed during user registration as well as on the public privacy page. Please note that in certain jurisdictions, there may be legal requirements mandating the disclosure of your data handling practices within this privacy policy.')
                                    ->schema([
                                        LivewireEntry::make('PrivacyStatementForm', PrivacyStatementForm::class)
                                            ->lazy(),
                                    ])
                                    ->aside(),
                            ]),
                        Tabs\Tab::make('Static Page')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                LivewireEntry::make('static_page', StaticPageTable::class)
                                    ->lazy(),
                            ]),
                    ])
                    ->contained(false),
            ]);
    }
}
