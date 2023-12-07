<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreatePlugin extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:plugin {name} {author}';

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
        $name = Str::studly($this->argument('name'));


        $author = $this->argument('author');


        $pluginDirectory = base_path('plugins' . DIRECTORY_SEPARATOR . $name);


        if (!File::exists(base_path('plugins'))) {
            File::makeDirectory(base_path('plugins'));
        }


        if (!File::exists($pluginDirectory)) {
            File::makeDirectory(base_path("/plugins/{$name}"));

            File::put(base_path("/plugins/{$name}/index.php"), "<?php\n\nuse Plugins\\{$name}\\{$name};\n\nreturn new {$name}();");

            File::put(base_path("/plugins/{$name}/{$name}.php"), $this->template($name));

            File::put(base_path("/plugins/{$name}/about.json"), $this->about($name, $author));

            return $this->info("Plugin {$name} created succesfully!");
        }

        return $this->info("Plugin {$name} already exists in " . base_path('plugins'));
    }

    public function template($name): string
    {
        return <<<EOD
        <?php

        namespace Plugins\\{$name};

        use App\Classes\Plugin;

        class {$name} extends Plugin
        {
            public \$aboutPlugin;

            public function boot()
            {
                // Stage is yours
            }

            public function onActivation()
            {
                // Runs on plugin activation
            }

            public function onDeactivation()
            {
                // Runs on plugin deactivation
            }

            public function onInstall()
            {
                // Runs on plugin installation
            }

            public function onUninstall()
            {
                // Runs on plugin uninstallation
            }
        }
        EOD;
    }

    public function about($name, $author): string
    {
        return <<<EOD
        {
            "plugin_name": "{$name}",
            "author": "{$author}",
            "description": "",
            "version": "1.0",
            "is_active": false
        }
        EOD;
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => 'Which name of plugin?',
            'author' => 'Which name of author?'
        ];
    }
}
