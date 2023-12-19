<?php

namespace App\Models\States\Interfaces;

interface SubmissionStateInterface
{
    public function fulfill(): void;

    public function acceptAbstract(): void;

    public function accept(): void;

    public function publish(): void;

    public function unpublish(): void;

    public function decline(): void;

    public function withdraw(): void;
}
