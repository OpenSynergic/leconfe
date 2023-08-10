<?php

namespace App\Enums\Users;

use App\Models\Enums\Concern\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string  implements HasLabel
{
  use UsefulEnums;

  case SuperAdmin = 'SuperAdmin';
  case Admin = 'Admin';
  case Editor = 'Editor';
  case Reviewer = 'Reviewer';
  case Author = 'Author';
  case Participant = 'Participant';

  public function getLabel(): ?string
  {
    return $this->name;
  }
}
