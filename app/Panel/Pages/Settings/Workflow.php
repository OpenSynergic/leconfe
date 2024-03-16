<?php

namespace App\Panel\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab;
use App\Infolists\Components\VerticalTabs\Tabs;
use App\Panel\Livewire\Workflows\AbstractSetting;
use App\Panel\Livewire\Workflows\EditingSetting;
use App\Panel\Livewire\Workflows\Payment\Tables\SubmissionPaymentItemTable;
use App\Panel\Livewire\Workflows\PaymentSetting;
use App\Panel\Livewire\Workflows\PeerReview\Forms\Guidelines;
use App\Panel\Livewire\Workflows\PeerReviewSetting;
use App\Panel\Pages\BasePage;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;

class Workflow extends BasePage implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static string $view = 'panel.pages.workflow';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Settings';

    public function booted(): void
    {
        abort_if(! static::canView(), 403);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canView() && static::$shouldRegisterNavigation;
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->can('Workflow:update');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make('Call for Abstract')
                        ->icon('iconpark-documentfolder-o')
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make('General')
                                        ->icon('iconpark-documentfolder-o')
                                        ->schema([
                                            LivewireEntry::make('abstract-setting')
                                                ->livewire(AbstractSetting::class),
                                        ]),
                                ]),
                        ]),
                    Tab::make('Payment')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make('General')
                                        ->schema([
                                            LivewireEntry::make('payment-setting')
                                                ->livewire(PaymentSetting::class),
                                        ]),
                                    HorizontalTab::make('Submission Payment Items')
                                        ->schema([
                                            LivewireEntry::make('payment-items')
                                                ->livewire(SubmissionPaymentItemTable::class),
                                        ]),
                                ]),

                        ]),
                    Tab::make('Peer Review')
                        ->icon('iconpark-search-o')
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make('General')
                                        ->icon('iconpark-documentfolder-o')
                                        ->schema([
                                            LivewireEntry::make('peer-review-setting')
                                                ->livewire(PeerReviewSetting::class)
                                                ->lazy(),
                                        ]),
                                    HorizontalTab::make('Reviewer Guidelines')
                                        ->icon('iconpark-docsuccess-o')
                                        ->schema([
                                            LivewireEntry::make('peer-review-setting')
                                                ->livewire(Guidelines::class)
                                                ->lazy(),
                                        ]),
                                    // HorizontalTab::make("Review Forms")
                                    //     ->icon("iconpark-formone-o")
                                    //     ->schema([
                                    //         LivewireEntry::make('peer-review-form-templates')
                                    //             ->livewire(FormTemplate::class)
                                    //             ->lazy()
                                    //     ])
                                ]),
                        ]),
                    Tab::make('Editing')
                        ->icon('iconpark-paperclip')
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make('General')
                                        ->icon('iconpark-documentfolder-o')
                                        ->schema([
                                            LivewireEntry::make('editing-setting')
                                                ->livewire(EditingSetting::class)
                                                ->lazy(),
                                        ]),
                                ]),
                        ]),
                ])
                ->maxWidth('full'),
        ]);
    }
}
