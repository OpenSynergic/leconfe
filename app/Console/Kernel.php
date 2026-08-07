<?php

namespace App\Console;

use App\Actions\Permissions\PermissionPersistAction;
use App\Actions\Permissions\PermissionPopulateAction;
use App\Actions\Leconfe\InstallAction;
use App\Actions\Leconfe\UpgradeAction;
use App\Actions\Leconfe\CheckVersionAction;
use App\Actions\Leconfe\CheckLatestVersion;
use App\Actions\Leconfe\QuickInstall;
use App\Actions\Leconfe\GetUpgradeActionHistory;
use App\Actions\Leconfe\Relink;
use App\Actions;
use App\Actions\Submissions\RemoveDeletedDiscussion;
use App\Models\UserInvitation;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by the application.
     *
     * @var array
     */
    protected $commands = [
        PermissionPersistAction::class,
        PermissionPopulateAction::class,
        InstallAction::class,
        UpgradeAction::class,
        CheckVersionAction::class,
        CheckLatestVersion::class,
        QuickInstall::class,
        GetUpgradeActionHistory::class,
        Relink::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            RemoveDeletedDiscussion::run();
        })->daily()->name('Remove deleted discussions');

        $schedule->call(function () {
            UserInvitation::query()
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);
        })->hourly()->name('Mark expired invitations');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
