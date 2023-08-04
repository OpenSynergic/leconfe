<?php

namespace App\Livewire\Panel\Wizards\SubmissionWizard\Contracts;

interface HasWizardStep
{
  public static function getWizardLabel(): string;
}
