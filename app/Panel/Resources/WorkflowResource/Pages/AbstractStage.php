<?php

namespace App\Panel\Resources\WorkflowResource\Pages;

use App\Panel\Resources\WorkflowResource;
use Filament\Resources\Pages\Page;

class AbstractStage extends Page
{
    protected static string $resource = WorkflowResource::class;

    protected static string $view = 'panel.resources.workflow-resource.pages.abstract-stage';
}
