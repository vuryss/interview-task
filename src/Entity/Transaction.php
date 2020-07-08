<?php

namespace App\Entity;

use Exception;

class Transaction
{
    private string $bin;
    private string $amount;
    private string $currency;

    public function __construct(string $bin, string $amount, string $currency)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @throws Exception
     *
     * @param object $data
     *
     * @return Transaction
     */
    public static function createFromObject(object $data): Transaction
    {
        if (!isset($data->bin, $data->amount, $data->currency)
            || !is_string($data->bin)
            || !is_string($data->amount)
            || !is_string($data->currency)
        ) {
            throw new Exception('Cannot create transaction from object!');
        }

        return new Transaction($data->bin, $data->amount, $data->currency);
    }
}
