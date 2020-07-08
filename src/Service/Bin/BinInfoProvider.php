<?php

namespace App\Service\Bin;

use App\Entity\BinInfo;

interface BinInfoProvider
{
    public function resolve(string $bin): BinInfo;
}
