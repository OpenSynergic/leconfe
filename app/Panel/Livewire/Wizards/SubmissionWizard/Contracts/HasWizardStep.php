<?php

namespace App\Panel\Livewire\Wizards\SubmissionWizard\Contracts;

interface HasWizardStep
{
    public static function getWizardLabel(): string;
}
