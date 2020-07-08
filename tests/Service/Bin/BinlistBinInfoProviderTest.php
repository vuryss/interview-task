<?php

namespace Service\Bin;

use App\Service\Bin\BinlistBinInfoProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BinlistBinInfoProviderTest extends TestCase
{
    public function testCanGetBinInfo()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode(['country' => ['alpha2' => 'DK']]));

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with($this->isType('string'))
            ->willReturn($response);

        $provider = new BinlistBinInfoProvider($httpClient);
        $binInfo = $provider->resolve('45717360');

        $this->assertEquals('DK', $binInfo->getCountryAlpha2());
    }
}
