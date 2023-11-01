<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreatePlugin extends Command
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

        if(!File::exists(base_path('plugins'))) {
            File::makeDirectory(base_path('plugins'));
        }

        File::makeDirectory(base_path("/plugins/{$name}"));
        File::put(base_path("/plugins/{$name}/index.php"), "<?php\n\nuse Plugins\\{$name}\\{$name};\n\nreturn new {$name}();");
        File::put(base_path("/plugins/{$name}/{$name}.php"), $this->template($name));
        File::put(base_path("/plugins/{$name}/about.json"), $this->about($name, $author));
    }

    public function template($name): string
    {
        return <<<EOD
        <?php

        namespace Plugins\\{$name};

        use App\Classes\Plugin;
        use App\Interfaces\HasPlugin;

        class {$name} extends Plugin
        {
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
            "version": "1.0"
        }
        EOD;
    }
}
