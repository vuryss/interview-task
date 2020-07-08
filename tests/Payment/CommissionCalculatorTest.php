<?php

namespace Payment;

use App\Entity\BinInfo;
use App\Entity\Transaction;
use App\Payment\CommissionCalculator;
use App\Service\Bin\BinInfoProvider;
use App\Service\CountryHelper;
use App\Service\CurrencyRate\CurrencyRateProvider;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    public function testCanCalculateCommissionForTransactionInEU()
    {
        $binInfo = new BinInfo();
        $binInfo->setCountryAlpha2('DK');

        $binInfoProvider = $this->createMock(BinInfoProvider::class);
        $binInfoProvider
            ->expects($this->once())
            ->method('resolve')
            ->with('test-bin')
            ->willReturn($binInfo);

        $countryHelper = $this->createMock(CountryHelper::class);
        $countryHelper
            ->expects($this->once())
            ->method('isCountryInEuropeanUnion')
            ->willReturn(true);

        $currencyRatesProvider = $this->createMock(CurrencyRateProvider::class);
        $currencyRatesProvider
            ->expects($this->once())
            ->method('getCurrencyRate')
            ->willReturn('1.00');

        $calculator = new CommissionCalculator(
            $binInfoProvider,
            $countryHelper,
            $currencyRatesProvider,
            ['eur' => 1, '_default' => 2]
        );

        $transaction = new Transaction('test-bin', '123.00', 'EUR');

        $commission = $calculator->calculateForTransaction($transaction);

        $this->assertEquals('1.23', $commission);
    }

    public function testCanCalculateCommissionForTransactionOutsideEU()
    {
        $binInfo = new BinInfo();
        $binInfo->setCountryAlpha2('JP');

        $binInfoProvider = $this->createMock(BinInfoProvider::class);
        $binInfoProvider
            ->expects($this->once())
            ->method('resolve')
            ->with('45417360')
            ->willReturn($binInfo);

        $countryHelper = $this->createMock(CountryHelper::class);
        $countryHelper
            ->expects($this->once())
            ->method('isCountryInEuropeanUnion')
            ->with('JP')
            ->willReturn(false);

        $currencyRatesProvider = $this->createMock(CurrencyRateProvider::class);
        $currencyRatesProvider
            ->expects($this->once())
            ->method('getCurrencyRate')
            ->with('JPY')
            ->willReturn('121.61');

        $calculator = new CommissionCalculator(
            $binInfoProvider,
            $countryHelper,
            $currencyRatesProvider,
            ['eur' => 1, '_default' => 2]
        );

        $transaction = new Transaction('45417360', '10000.00', 'JPY');

        $commission = $calculator->calculateForTransaction($transaction);

        $this->assertEquals('1.65', $commission);
    }
}
