<?php

namespace Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

use Illuminate\Filesystem\Filesystem;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use Rahmanramsi\LivewirePageGroup\Pages\Page;
use ReflectionClass;

trait HasLivewireComponents
{
    /**
     * @var array<string, class-string>
     */
    protected array $livewireComponents = [];

    /**
     * @var array<class-string>
     */
    protected array $pages = [];

    /**
     * @var array<string>
     */
    protected array $pageDirectories = [];

    /**
     * @var array<string>
     */
    protected array $pageNamespaces = [];

    /**
     * @param  array<class-string>  $pages
     */
    public function pages(array $pages): static
    {
        $this->pages = [
            ...$this->pages,
            ...$pages,
        ];

        foreach ($pages as $page) {
            $this->queueLivewireComponentForRegistration($page);
        }

        return $this;
    }

    /**
     * @return array<class-string>
     */
    public function getPages(): array
    {
        return array_unique($this->pages);
    }

    public function discoverPages(string $in, string $for): static
    {
        $this->pageDirectories[] = $in;
        $this->pageNamespaces[] = $for;

        $this->discoverComponents(
            Page::class,
            $this->pages,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function discoverLivewireComponents(string $in, string $for): static
    {
        $discover = [];

        $this->discoverComponents(
            Component::class,
            $discover,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @param  array<string, class-string<Component>>  $register
     */
    protected function discoverComponents(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! str($directory)->contains('*'))) {
            return;
        }

        $namespace = str($namespace);

        foreach ($filesystem->allFiles($directory) as $file) {
            $variableNamespace = $namespace->contains('*') ? str_ireplace(
                ['\\'.$namespace->before('*'), $namespace->after('*')],
                ['', ''],
                str($file->getPath())
                    ->after(base_path())
                    ->replace(['/'], ['\\']),
            ) : null;

            if (is_string($variableNamespace)) {
                $variableNamespace = (string) str($variableNamespace)->before('\\');
            }

            $class = (string) $namespace
                ->append('\\', $file->getRelativePathname())
                ->replace('*', $variableNamespace)
                ->replace(['/', '.php'], ['\\', '']);

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            if (is_subclass_of($class, Component::class)) {
                $this->queueLivewireComponentForRegistration($class);
            }

            if (! is_subclass_of($class, $baseClass)) {
                continue;
            }

            if (method_exists($class, 'isDiscovered') && ! $class::isDiscovered()) {
                continue;
            }

            $register[] = $class;
        }
    }

    protected function queueLivewireComponentForRegistration(string $component): void
    {
        $componentName = app(ComponentRegistry::class)->getName($component);

        $this->livewireComponents[$componentName] = $component;
    }

    protected function registerLivewireComponents(): void
    {
        foreach ($this->livewireComponents as $componentName => $componentClass) {
            Livewire::component($componentName, $componentClass);
        }

        $this->livewireComponents = [];
    }

    /**
     * @return array<string>
     */
    public function getPageDirectories(): array
    {
        return $this->pageDirectories;
    }

    /**
     * @return array<string>
     */
    public function getPageNamespaces(): array
    {
        return $this->pageNamespaces;
    }
}
