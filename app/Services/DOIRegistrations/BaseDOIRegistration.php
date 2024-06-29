<?php

namespace App\Services\DOIRegistrations;

use App\Interfaces\DOIRegistrationDriver;
use App\Models\Submission;

abstract class BaseDOIRegistration implements DOIRegistrationDriver
{
	public function updateSettings(array $settings)
	{
	}
}
