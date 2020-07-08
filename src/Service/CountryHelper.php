<?php

namespace App\Service;

class CountryHelper
{
    private array $europeanCountries = [];

    public function __construct(array $europeanCountries)
    {
        $this->europeanCountries = $europeanCountries;
    }

    public function isCountryInEuropeanUnion(string $alpha2)
    {
        // TODO: If we would only look up by alpha2 representation, then map of alpha2 => country will be faster
        foreach ($this->europeanCountries as $country) {
            if ($country['alpha2'] === $alpha2) {
                return true;
            }
        }

        return false;
    }
}
