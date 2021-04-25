<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

use App\Enums\FileScenario;
use Illuminate\Support\Facades\App;
use Ozerich\FileStorage\Storage;

class CsvReader
{
    private static function detectDelimiter(string $filePath): ?string
    {
        if (!is_file($filePath)) {
            return null;
        }

        $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

        $handle = fopen($filePath, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        if (array_sum($delimiters) <= count($delimiters)) {
            return null;
        }

        return array_search(max($delimiters), $delimiters);
    }

    public static function loadFromFile(string $filePath, bool $isWindows1251 = false): ?array
    {
        if (!is_file($filePath)) {
            return null;
        }

        $delimiter = self::detectDelimiter($filePath);

        $f = fopen($filePath, 'r+');
        $result = [];
        while (($row = fgetcsv($f, 100000, $delimiter))) {
            if ($isWindows1251) {
                foreach ($row as &$cell) {
                    $cell = mb_convert_encoding($cell, 'UTF-8', 'Windows-1251');
                }
            }
            $result[] = $row;
        }
        fclose($f);

        return $result;
    }
}
