<?php

namespace Rahmanramsi\LivewirePageGroup;

use Illuminate\Support\ServiceProvider;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

abstract class PageGroupServiceProvider extends ServiceProvider
{
    abstract public function pageGroup(PageGroup $pageGroup): PageGroup;

    public function register()
    {
        $this->app->resolving('livewire-page-group', function () {
            LivewirePageGroup::registerPageGroup(
                $this->pageGroup(PageGroup::make()),
            );
        });
    }
}
