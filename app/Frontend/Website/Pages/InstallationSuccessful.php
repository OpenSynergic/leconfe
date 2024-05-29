<?php

namespace App\Frontend\Website\Pages;

use App\Facades\Block as BlockFacade;
use App\Facades\MetaTag;
use App\Facades\SidebarFacade;
use App\Models\Conference;
use App\Models\Enums\SerieState;
use App\Models\Serie;
use App\Models\Sponsor;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\Page;

class InstallationSuccessful extends Page
{
    protected static string $view = 'frontend.website.pages.installation-successful';


    public function mount()
    {
        MetaTag::add('robots', 'noindex, nofollow');
    }

    public static function getLayout(): string
    {
        return 'frontend.website.components.layouts.base';
    }
}
