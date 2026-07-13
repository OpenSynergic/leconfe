<?php

namespace Rahmanramsi\LivewirePageGroup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

class SetUpPageGroup
{
    public function handle(Request $request, Closure $next, string $pageGroup): mixed
    {
        $pageGroup = LivewirePageGroup::getPageGroup($pageGroup);

        LivewirePageGroup::setCurrentPageGroup($pageGroup);

        LivewirePageGroup::bootCurrentPageGroup();

        return $next($request);
    }
}
