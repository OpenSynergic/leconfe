<?php

namespace App\Interfaces;

use App\Models\Submission;

interface DOIRegistrationDriver
{
    public function getName(): string;

    public function getTableActions(): array;

    public function getSettingFormSchema(): array;

    public function updateSettings(array $settings);

	public function exportXml(Submission $submission);

	public function depositXml(Submission $submission);
}
