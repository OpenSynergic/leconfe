<?php

namespace App\Enums\Users;

enum UserRole: string
{
  case SuperAdmin = 'super-admin';
  case Admin = 'admin';
  case Editor = 'editor';
  case Reviewer = 'reviewer';
  case Author = 'author';
}
