<?php

namespace App\Entity;

class BinInfo
{
    private string $countryAlpha2;

    public function getCountryAlpha2(): string
    {
        return $this->countryAlpha2;
    }

    public function setCountryAlpha2(string $countryAlpha2): BinInfo
    {
        $this->countryAlpha2 = $countryAlpha2;

        return $this;
    }
}
