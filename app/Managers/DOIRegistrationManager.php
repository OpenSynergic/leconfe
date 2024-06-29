<?php

namespace App\Managers;

use App\Interfaces\DOIRegistrationDriver;
use App\Services\DOIRegistrations\CrossrefDOIRegistration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class DOIRegistrationManager extends Manager
{
    public function getDefaultDriver(): ?string
    {
        return App::getCurrentConference()?->getMeta('doi_registration_agency');
    }

    public function createCrossrefDriver(): DOIRegistrationDriver
    {
        return new CrossrefDOIRegistration;
    }

    public function getAllDriverNames(): \Illuminate\Support\Collection
    {
        return collect(['crossref' => 'crossref', ...$this->customCreators])->mapWithKeys(function ($driver, $key) {
            return [$key => $this->driver($key)->getName()];
        });
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
