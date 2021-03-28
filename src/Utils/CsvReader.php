<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

use App\Enums\FileScenario;
use Illuminate\Support\Facades\App;
use Ozerich\FileStorage\Storage;

class CsvReader
{
    public static function loadFromFile(string $filePath, bool $isWindows1251 = false): ?array
    {
        if (!is_file($filePath)) {
            return null;
        }

        $f = fopen($filePath, 'r+');
        $result = [];
        while (($row = fgetcsv($f, 100000, ';'))) {
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
