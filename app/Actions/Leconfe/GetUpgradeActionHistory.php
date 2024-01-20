<?php

namespace App\Actions\Leconfe;

use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Activitylog\Models\Activity;

use function Laravel\Prompts\spin;

class GetUpgradeActionHistory
{
    use AsAction;

    public function handle(): Collection
    {
        return Activity::where('log_name', 'leconfe')
            ->where('event', 'upgrade')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function asCommand(Command $command): void
    {
        $upgradeHistories = spin(fn () => $this->handle(), 'Getting upgrade history');

        if ($upgradeHistories->isEmpty()) {
            $command->info('No upgrade history found');

            return;
        }

        $command->table(
            ['From', 'To', 'Duration', 'Status', 'Run at'],
            $upgradeHistories->map(function ($history) {
                return [
                    $history->getExtraProperty('from'),
                    $history->getExtraProperty('to'),
                    CarbonInterval::seconds($history->getExtraProperty('duration')),
                    $history->getExtraProperty('status'),
                    $history->created_at,
                ];
            })
        );
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:upgrade-history';
    }

    public function getCommandDescription(): string
    {
        return 'Check leconfe upgrade history';
    }
}
