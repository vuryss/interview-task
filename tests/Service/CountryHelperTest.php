<?php

namespace Service;

use App\Service\CountryHelper;
use PHPUnit\Framework\TestCase;

class CountryHelperTest extends TestCase
{
    public function testIsCountryInEU()
    {
        $helper = new CountryHelper([['name' => 'Test Country', 'alpha2' => 'TC']]);
        $this->assertEquals(false, $helper->isCountryInEuropeanUnion('BG'));
        $this->assertEquals(true, $helper->isCountryInEuropeanUnion('TC'));
    }
}
