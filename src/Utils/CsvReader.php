<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

use App\Enums\FileScenario;
use Illuminate\Support\Facades\App;
use Ozerich\FileStorage\Storage;

class CsvReader
{
    public static function loadFromFile(string $filePath): ?array
    {
        if (!is_file($filePath)) {
            return null;
        }

        $f = fopen($filePath, 'r+');
        $result = [];
        while (($row = fgetcsv($f))) {
            $result[] = $row;
        }
        fclose($f);

        return $result;
    }
}
