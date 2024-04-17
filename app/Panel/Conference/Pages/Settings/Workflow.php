<?php

namespace App\Panel\Conference\Pages\Settings;

use App\Infolists\Components\LivewireEntry;
use App\Infolists\Components\VerticalTabs\Tab;
use App\Infolists\Components\VerticalTabs\Tabs;
use App\Panel\Conference\Livewire\Tables\AuthorRoleTable;
use App\Panel\Conference\Livewire\Workflows\AbstractSetting;
use App\Panel\Conference\Livewire\Workflows\EditingSetting;
use App\Panel\Conference\Livewire\Workflows\Payment\Tables\SubmissionPaymentItemTable;
use App\Panel\Conference\Livewire\Workflows\PaymentSetting;
use App\Panel\Conference\Livewire\Workflows\PeerReview\Forms\Guidelines;
use App\Panel\Conference\Livewire\Workflows\PeerReviewSetting;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Tabs as HorizontalTabs;
use Filament\Infolists\Components\Tabs\Tab as HorizontalTab;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;

class Workflow extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?int $navigationSort = 1;

    protected static string $view = 'panel.conference.pages.workflow';

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Settings';

    public function mount()
    {
        //
    }

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
                    Tab::make('General')
                        ->icon('heroicon-o-bookmark-square')
                        ->schema([
                            HorizontalTabs::make()
                                ->tabs([
                                    HorizontalTab::make('Author Roles')
                                        ->icon('heroicon-o-users')
                                        ->extraAttributes(['class' => '!p-0'])
                                        ->schema([
                                            LivewireEntry::make('author-roles')
                                                ->livewire(AuthorRoleTable::class),
                                        ]),
                                ]),
                        ]),
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
