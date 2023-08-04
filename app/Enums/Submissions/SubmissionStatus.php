<?php

namespace App\Enums\Submissions;

enum SubmissionStatus: string
{
  case Wizard = 'wizard';
  case Active = 'active';
  case Review = 'review';
  case Accepted = 'accepted';
  case Published = 'published';
  case Declined = 'declined';
}
