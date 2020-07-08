<?php

namespace App\Payment;

use App\Entity\Transaction;
use App\Service\Bin\BinInfoProvider;
use App\Service\CountryHelper;
use App\Service\CurrencyRate\CurrencyRateProvider;

class CommissionCalculator
{
    private BinInfoProvider       $binInfoProvider;
    private CountryHelper         $countryHelper;
    private CurrencyRateProvider  $currencyRateProvider;
    private array                 $commissionPercentages;

    public function __construct(
        BinInfoProvider $binInfoProvider,
        CountryHelper $countryHelper,
        CurrencyRateProvider $currencyRateProvider,
        array $commissionPercentages
    ) {
        $this->binInfoProvider       = $binInfoProvider;
        $this->countryHelper         = $countryHelper;
        $this->currencyRateProvider  = $currencyRateProvider;
        $this->commissionPercentages = $commissionPercentages;
    }

    public function calculateForTransaction(Transaction $transaction)
    {
        $binInfo              = $this->binInfoProvider->resolve($transaction->getBin());
        $commissionPercentage = $this->getCommissionRateForCountry($binInfo->getCountryAlpha2());
        $rate                 = $this->currencyRateProvider->getCurrencyRate($transaction->getCurrency());

        $amountInEUR       = bcdiv($transaction->getAmount(), $rate, 10);
        $commission        = bcmul($amountInEUR, $commissionPercentage / 100, 10);
        $commissionInCents = ceil(bcmul($commission, 100, 1));

        return bcdiv($commissionInCents, 100, 2);
    }

    private function getCommissionRateForCountry(string $alpha2): string
    {
        if ($this->countryHelper->isCountryInEuropeanUnion($alpha2)) {
            return $this->commissionPercentages['eur'];
        }

        return $this->commissionPercentages['_default'];
    }
}
