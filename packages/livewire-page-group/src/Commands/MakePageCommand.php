<?php

namespace Rahmanramsi\LivewirePageGroup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Rahmanramsi\LivewirePageGroup\Commands\Concerns\CanManipulateFiles;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroup;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakePageCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new LivewirePageGroup Page class and view';

    protected $signature = 'make:livewire-page {name?} {--group=} {--F|force}';

    public function handle(): int
    {
        $page = (string) str(
            $this->argument('name') ??
                text(
                    label: 'What is the page name?',
                    placeholder: 'EditSettings',
                    required: true,
                ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) str($page)->afterLast('\\');
        $pageNamespace = str($page)->contains('\\') ?
            (string) str($page)->beforeLast('\\') :
            '';

        $pageGroup = $this->option('group');

        if ($pageGroup) {
            $pageGroup = LivewirePageGroup::getPageGroup($pageGroup);
        }

        if (! $pageGroup) {
            $pageGroups = LivewirePageGroup::getPageGroups();
            /** @var PageGroup $pageGroup */
            $pageGroup = (count($pageGroups) > 1) ? $pageGroups[select(
                label: 'Which page group would you like to create this in?',
                options: array_map(
                    fn (PageGroup $pageGroup): string => $pageGroup->getId(),
                    $pageGroups,
                ),
            )] : Arr::first($pageGroups);
        }

        if (! $pageGroup) {
            $this->components->error('No page group found.');

            return static::FAILURE;
        }

        $pageDirectories = $pageGroup->getPageDirectories();
        $pageNamespaces = $pageGroup->getPageNamespaces();

        $namespace = (count($pageNamespaces) > 1) ?
            select(
                label: 'Which namespace would you like to create this in?',
                options: $pageNamespaces
            ) : (Arr::first($pageNamespaces) ?? 'App\\PageGroup\\Pages');
        $path = (count($pageDirectories) > 1) ?
            $pageDirectories[array_search($namespace, $pageNamespaces)] : (Arr::first($pageDirectories) ?? app_path('PageGroup/Pages/'));

        $view = str($page)
            ->prepend(
                (string) str("{$namespace}\\")
                    ->replaceFirst('App\\', '')
            )
            ->replace('\\', '/')
            ->explode('/')
            ->map(fn ($segment) => Str::of($segment)->kebab()->lower())
            ->implode('.');

        $path = (string) str($page)
            ->prepend('/')
            ->prepend(empty($resource) ? ($path ?? '') : ($resourcePath ?? '')."\\{$resource}\\Pages\\")
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $files = [
            $path,
        ];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('Page', $path, [
            'class' => $pageClass,
            'namespace' => str($namespace ?? '').($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
            'view' => $view,
        ]);

        $this->copyStubToApp('PageView', $viewPath);

        $this->components->info("Successfully created {$page}!");

        return static::SUCCESS;
    }
}
