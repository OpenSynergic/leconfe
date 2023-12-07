<?php

namespace Database\Seeders\Productions;

use App\Actions\SubmissionFiles\FilesTypePopulateAction;
use Illuminate\Database\Seeder;

class SubmissionFileTypeSeeder extends Seeder
{
    public function run(): void
    {
        FilesTypePopulateAction::run();
    }
}
