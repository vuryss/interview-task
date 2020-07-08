<?php

namespace App\Service\CurrencyRate;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class ExchangeRateApiProvider implements CurrencyRateProvider
{
    public const BASE_CURRENCY = 'EUR';

    private HttpClientInterface $httpClient;
    private array               $cache = [];

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws Throwable
     *
     * @param string $currency
     *
     * @return string
     */
    public function getCurrencyRate(string $currency): string
    {
        if ($currency === self::BASE_CURRENCY) {
            return '1.0000000000';
        }

        $rates = $this->getCurrencyRates();

        if (!isset($rates[$currency])) {
            throw new Exception('Currency rate not found for currency: ' . $currency);
        }

        return $rates[$currency];
    }

    /**
     * @throws Throwable
     *
     * @return array
     */
    private function getCurrencyRates(): array
    {
        if (empty($this->cache)) {
            $response = $this->resolveFromProvider();

            if (empty($response->rates)) {
                throw new Exception('Could not resolve currency rates');
            }

            foreach ($response->rates as $currency => $rate) {
                $this->cache[$currency] = bcdiv($rate, 1, 10);
            }
        }

        return $this->cache;
    }

    /**
     * @throws Throwable
     *
     * @return object
     */
    private function resolveFromProvider(): object
    {
        $response = $this->httpClient->request(
            'GET',
            'https://api.exchangeratesapi.io/latest?base=' . self::BASE_CURRENCY
        );

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Could not resolve currency rates');
        }

        $responseBody = json_decode($response->getContent());

        if (empty($responseBody) || json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Could not resolve currency rates');
        }

        return $responseBody;
    }
}
