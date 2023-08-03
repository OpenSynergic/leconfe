<?php

namespace App\Livewire\Wizards\SubmissionWizard\Contracts;

interface HasWizardStep
{
  public static function getWizardLabel(): string;
}
