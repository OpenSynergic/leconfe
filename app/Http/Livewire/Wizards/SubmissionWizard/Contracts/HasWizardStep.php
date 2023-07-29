<?php

namespace App\Http\Livewire\Wizards\SubmissionWizard\Contracts;

interface HasWizardStep
{
  public static function getWizardLabel(): string;
}
