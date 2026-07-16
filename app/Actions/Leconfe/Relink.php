<?php

namespace App\Actions\Leconfe;

use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class Relink
{
    use AsAction;

    public function handle()
    {
        try {
            $storageLink = public_path('storage');

            if (file_exists($storageLink) && is_link($storageLink)) {
                File::delete($storageLink);
            } elseif (file_exists($storageLink) && ! is_link($storageLink)) {
                File::deleteDirectory($storageLink) || File::delete($storageLink);
            }

            // Create a new storage link
            if (! file_exists($storageLink)) {
                File::relativeLink(storage_path('app/public'), $storageLink);
            }

            $pluginLink = public_path('plugin');
            // Delete all folders in plugin folder except .gitignore
            if (file_exists($pluginLink) && is_dir($pluginLink)) {
                foreach (File::directories($pluginLink) as $dir) {
                    File::delete($dir);
                }
                foreach (File::allFiles($pluginLink) as $file) {
                    if ($file->getFilename() === '.gitignore') {
                        continue;
                    }
                    File::delete($file);
                }
            }
        } catch (Throwable $th) {
            Log::warning($th->getMessage());
        }
    }

    public function asCommand(Command $command): void
    {
        $command->info('Relinking storage and plugin folder...');

        $this->handle();

        $command->info('Relinked storage and plugin folder.');
    }

    public function getCommandSignature(): string
    {
        return 'leconfe:relink';
    }

    public function getCommandDescription(): string
    {
        return 'Install leconfe application';
    }
}
