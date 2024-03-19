<?php

namespace App\Panel\Administration\Pages;

use Filament\Facades\Filament;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Dashboard extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-m-home';

    protected static ?string $title = 'Administration';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'panel.administration.pages.dashboard';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('')
                    ->schema([
                        Actions::make([
                            Action::make('System Information')
                                ->icon('heroicon-m-cog-8-tooth')
                                ->color('primary')
                                ->requiresConfirmation()
                                ->outlined()
                                ->extraAttributes(['class' => 'w-48'])
                                ->url(route('phpmyinfo')),

                        ]),
                        Actions::make([
                            Action::make('Expire User Session')
                                ->icon('heroicon-m-user')
                                ->color('primary')
                                ->requiresConfirmation()
                                ->outlined()
                                ->successNotification(
                                    Notification::make()
                                        ->success()
                                        ->title('Session cleared')
                                        ->body('User session cleared succesfully'),
                                )
                                ->extraAttributes(['class' => 'w-48'])
                                ->action(fn (Action $action) => $this->expireUserSession($action)),

                        ]),
                        Actions::make([
                            Action::make('Clear Data Caches')
                                ->icon('heroicon-m-circle-stack')
                                ->color('primary')
                                ->requiresConfirmation()
                                ->outlined()
                                ->successNotification(
                                    Notification::make()
                                        ->success()
                                        ->title('Clear succesfully')
                                        ->body('Data caches cleared succesfully'),
                                )
                                ->extraAttributes(['class' => 'w-48'])
                                ->action(function (Action $action) {
                                    $this->runArtisanCommand('cache:clear', $action);
                                    $this->runArtisanCommand('optimize:clear', $action);
                                }),
                        ]),
                        Actions::make([
                            Action::make('Clear View Caches')
                                ->icon('heroicon-m-trash')
                                ->color('primary')
                                ->requiresConfirmation()
                                ->outlined()
                                ->successNotification(
                                    Notification::make()
                                        ->success()
                                        ->title('Clear succesfully')
                                        ->body('View caches cleared succesfully'),
                                )
                                ->extraAttributes(['class' => 'w-48'])
                                ->action(function (Action $action) {
                                    $this->runArtisanCommand('view:clear', $action);
                                    $this->runArtisanCommand('icons:clear', $action);
                                    $this->runArtisanCommand('icons:cache', $action);
                                }),
                        ]),
                    ]),
            ]);
    }

    protected function expireUserSession(Action $action)
    {
        try {
            $userAuth = Filament::auth()->user();

            Session::flush();

            Auth::login($userAuth);

            session()->regenerate();

            $action->sendSuccessNotification();

            $this->redirect(Filament::getUrl());
        } catch (\Throwable $th) {
            $action->sendFailureNotification();
        }
    }

    protected function runArtisanCommand($command, Action $action)
    {
        try {
            Artisan::call($command);

            $action->sendSuccessNotification();
        } catch (\Throwable $th) {
            $action->sendFailureNotification();
        }
    }
}
