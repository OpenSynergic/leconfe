<?php

namespace Rahmanramsi\LivewirePageGroup;

use Closure;
use Rahmanramsi\LivewirePageGroup\Components\Component;
use Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

class PageGroup extends Component
{
    use Concern\HasHomePage,
        Concern\HasId,
        Concern\HasLayout,
        Concern\HasLivewireComponents,
        Concern\HasMiddleware,
        Concern\HasRoutes;

    protected ?Closure $bootUsing = null;

    public static function make(): static
    {
        $static = app(static::class);

        return $static;
    }

    public function register(): void
    {
        $this->registerLivewireComponents();
        $this->registerLivewirePersistentMiddleware();
    }

    public function boot(): void
    {
        if ($callback = $this->bootUsing) {
            $callback($this);
        }
    }

    public function bootUsing(?Closure $callback): static
    {
        $this->bootUsing = $callback;

        return $this;
    }
}
