<?php

namespace App\Service\CurrencyRate;

interface CurrencyRateProvider
{
    public function getCurrencyRate(string $currency): string;
}
