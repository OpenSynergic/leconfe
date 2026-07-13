<?php

use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

Route::name('livewirePageGroup.')
    ->group(function () {
        foreach (LivewirePageGroup::getPageGroups() as $pageGroup) {
            /** @var \Rahmanramsi\LivewirePageGroup\PageGroup $pageGroup */
            $pageGroupId = $pageGroup->getId();

            Route::domain($pageGroup->getDomain())
                ->middleware($pageGroup->getMiddleware())
                ->name("{$pageGroupId}.")
                ->prefix($pageGroup->getPath())
                ->group(function () use ($pageGroup) {
                    Route::name('pages.')->group(function () use ($pageGroup): void {
                        Route::get('/', $pageGroup->getHomePage())
                            ->name('home');

                        foreach ($pageGroup->getPages() as $page) {
                            $page::routes($pageGroup);
                        }
                    });

                    if ($routes = $pageGroup->getRoutes()) {
                        $routes($pageGroup);
                    }
                });
        }
    });
