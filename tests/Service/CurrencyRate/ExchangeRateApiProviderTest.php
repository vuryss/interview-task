<?php

namespace Service\CurrencyRate;

use App\Service\CurrencyRate\ExchangeRateApiProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRateApiProviderTest extends TestCase
{
    public function testCanGetFlatCurrencyRateForEUR()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        $provider = new ExchangeRateApiProvider($httpClient);
        $rate = $provider->getCurrencyRate('EUR');

        $this->assertEquals('1.0000000000', $rate);
    }

    public function testCanGetCurrencyRateForUSD()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode(['rates' => ['USD' => 1.129]]));

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with($this->isType('string'))
            ->willReturn($response);

        $provider = new ExchangeRateApiProvider($httpClient);
        $rate = $provider->getCurrencyRate('USD');

        $this->assertEquals('1.1290000000', $rate);
    }
}
