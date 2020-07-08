<?php

namespace App\Service\Bin;

use App\Entity\BinInfo;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class BinlistBinInfoProvider implements BinInfoProvider
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws Throwable
     *
     * @param string $bin
     *
     * @return BinInfo
     */
    public function resolve(string $bin): BinInfo
    {
        $responseData = $this->resolveFromBinlist($bin);

        if (empty($responseData->country->alpha2)) {
            throw new Exception('Could not resolve bin info for: ' . $bin);
        }

        $binInfo = new BinInfo();
        $binInfo->setCountryAlpha2($responseData->country->alpha2);

        return $binInfo;
    }

    /**
     * @throws Throwable
     *
     * @param string $bin
     *
     * @return object
     */
    private function resolveFromBinlist(string $bin): object
    {
        $response = $this->httpClient->request(
            'GET',
            'https://lookup.binlist.net/' . $bin
        );

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Could not resolve bin info for: ' . $bin);
        }

        $responseBody = json_decode($response->getContent());

        if (empty($responseBody) || json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Could not resolve bin info for: ' . $bin);
        }

        return $responseBody;
    }
}
