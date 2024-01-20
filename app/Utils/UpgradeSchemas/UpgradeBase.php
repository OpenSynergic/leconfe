<?php

namespace App\Utils\UpgradeSchemas;

abstract class UpgradeBase
{
    public function __invoke()
    {
        $this->run();
    }

    public function __construct(
        public string $databaseVersion,
        public string $applicationVersion,
    ) {

    }

    abstract public function run(): void;
}
