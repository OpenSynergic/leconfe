<?php

namespace App\Console\Commands;

use App\Facades\Plugin;
use App\Models\Conference;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreatePlugin extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Leconfe plugin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pluginName = text(
            label: 'What is the plugin name?',
            required: true
        );

        $pluginFolder = Str::studly($pluginName);

        $author = text(
            label: 'What is the author name?',
            required: true
        );

        $description = text(
            label: 'What is the plugin description?',
            required: false,
        );

        // $conferenceId = select(
        //     label: 'Where the plugin will be installed?',
        //     options: Conference::all()
        //         ->pluck('name', 'id')
        //         ->prepend('Website', 0),
        // );

        // if ($conferenceId) {
        //     app()->setCurrentConference(Conference::find($conferenceId));
        // }

        $pluginDisk = Plugin::getDisk();

        if (! File::exists($pluginDisk->path($pluginFolder))) {
            File::makeDirectory($pluginDisk->path("{$pluginFolder}"));

            File::put($pluginDisk->path($pluginFolder.DIRECTORY_SEPARATOR.'index.php'), $this->template());

            File::put($pluginDisk->path($pluginFolder.DIRECTORY_SEPARATOR.'index.yaml'), Yaml::dump([
                'name' => $pluginName,
                'folder' => $pluginFolder,
                'author' => $author,
                'description' => $description,
                'version' => '1.0.0',
            ]));

            return $this->info("Plugin {$pluginFolder} created succesfully!");
        }

        return $this->info("Plugin {$pluginName} already exists in ".base_path('plugins'));
    }

    public function template(): string
    {
        return <<<EOD
        <?php

        use App\Classes\Plugin;

        return new class extends Plugin
        {
            public function boot()
            {
            }
        };
        EOD;
    }
}
