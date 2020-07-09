<?php

namespace App\Parser;

use Exception;
use Generator;

/**
 * @codeCoverageIgnore
 */
class JsonDataFileParser implements FileParser
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     *
     * @param string $filePath
     *
     * @return Generator
     */
    public function parseFile(string $filePath): Generator
    {
        try {
            $file = $this->openFile($filePath);

            while ($line = fgets($file)) {
                yield $this->parseJsonString(trim($line));
            }
        } finally {
            if (isset($file) && is_resource($file)) {
                $this->closeFile($file);
            }
        }
    }

    /**
     * @throws Exception
     *
     * @param string $filePath
     *
     * @return false|resource
     */
    private function openFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File: ' . $filePath . ' does not exist!');
        }

        $file = fopen($filePath, 'r');

        if (!$file) {
            throw new Exception('Cannot open: ' . $filePath . ' for reading. Check permissions?');
        }

        if (!flock($file, LOCK_SH)) {
            throw new Exception('File: ' . $filePath . ' is already in use!');
        }

        return $file;
    }

    /**
     * @param resource $file
     */
    private function closeFile($file)
    {
        flock($file, LOCK_UN);
        fclose($file);
    }

    /**
     * @throws Exception
     *
     * @param string $json
     *
     * @return object
     */
    private function parseJsonString(string $json): object
    {
        $data = json_decode($json);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid json data in given file!');
        }

        return $data;
    }
}
