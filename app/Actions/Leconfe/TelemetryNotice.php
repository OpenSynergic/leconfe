<?php

namespace App\Actions\Leconfe;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

final class TelemetryNotice
{
    public static function chooseForInstallation(Command $command): bool
    {
        self::show($command);

        if ($command->option('without-telemetry')) {
            $command->info('Telemetry is disabled. You can change this later in Administration settings.');

            return false;
        }

        if ($command->getDefinition()->hasOption('confirm') && $command->option('confirm')) {
            return true;
        }

        return confirm('Enable installation and usage telemetry?', default: true);
    }

    public static function showUpgrade(Command $command, bool $withoutTelemetry): void
    {
        $command->info('This upgrade enables one daily aggregate installation and usage snapshot for internal roadmap and feature-adoption decisions. The data is linked to this installation and is not anonymous.');
        $command->info('Sent: installation ID, base URL/domain, admin name and email, IP address, environment/plugin versions, and aggregate workflow and feature counts.');
        $command->info('Never sent: submission titles/content, participant/author/reviewer identities, review content, filenames/file content, payment amounts/currencies/methods, invoice/receipt numbers, or payer identity.');
        $command->info('Opting out stops future sends but does not delete historical data. Use --without-telemetry or Administration settings.');

        if ($withoutTelemetry) {
            $command->info('Telemetry opt-out requested; future sends will remain disabled.');
        }
    }

    private static function show(Command $command): void
    {
        $command->info('Leconfe sends one daily aggregate installation and usage snapshot to guide internal roadmap and feature-adoption decisions. The data is linked to this installation and is not anonymous.');
        $command->info('Sent: installation ID, base URL/domain, admin name and email, IP address, environment/plugin versions, and aggregate workflow and feature counts.');
        $command->info('Never sent: submission titles/content, participant/author/reviewer identities, review content, filenames/file content, payment amounts/currencies/methods, invoice/receipt numbers, or payer identity.');
        $command->info('Opting out stops future sends but does not delete historical data. Use --without-telemetry or Administration settings.');
    }
}
