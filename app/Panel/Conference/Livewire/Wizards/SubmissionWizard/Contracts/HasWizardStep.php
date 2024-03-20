<?php

namespace App\Panel\Conference\Livewire\Wizards\SubmissionWizard\Contracts;

interface HasWizardStep
{
    public static function getWizardLabel(): string;
}
