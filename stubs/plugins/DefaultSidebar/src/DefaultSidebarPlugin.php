<?php

namespace DefaultSidebar;

use App\Classes\Plugin;
use App\Facades\SidebarFacade;

class DefaultSidebarPlugin extends Plugin
{
    public function boot()
    {
        SidebarFacade::register($this->getSidebars());
    }

    protected function getSidebars()
    {
        $conferenceId = app()->getCurrentConferenceId();

        $sidebars = [
           
        ];

        if($conferenceId){
            $sidebars = array_merge($sidebars, [
                new Sidebar\SubmitNowSidebar,
                new Sidebar\CommitteeSidebar,
                new Sidebar\TopicsSidebar,
                new Sidebar\PreviousEventSidebar,
                new Sidebar\TimelineSidebar,
            ]);
        }

        return $sidebars;
    }
}