<?php

namespace App\Parser;

use Generator;

interface FileParser
{
    public function parseFile(string $filePath): Generator;
}
