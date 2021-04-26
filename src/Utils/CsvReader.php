<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

class CsvReader
{
    private static function removeBomUtf8($s)
    {
        if (substr($s, 0, 3) == chr(hexdec('EF')) . chr(hexdec('BB')) . chr(hexdec('BF'))) {
            return substr($s, 3);
        } else {
            return $s;
        }
    }

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

    private static function convertToUTF(string $s)
    {
        $encoding = mb_detect_encoding($s, "UTF-8,ISO-8859-1,WINDOWS-1251");

        if ($encoding != 'UTF-8') {
            return mb_convert_encoding($s, 'UTF-8', 'Windows-1251');
        } else {
            return self::removeBomUtf8($s);
        }
    }

    private static function fgetcsvUTF8(&$handle, $length, $separator = ';'): ?array
    {
        if (($buffer = fgets($handle, $length)) !== false) {
            $buffer = self::convertToUTF($buffer);
            return str_getcsv($buffer, $separator);
        }

        return null;
    }


    public static function loadFromFile(string $filePath, ?string $delimiter = null): ?array
    {
        if (!is_file($filePath)) {
            return null;
        }

        ini_set("auto_detect_line_endings", true);

        if (is_null($delimiter)) {
            $delimiter = self::detectDelimiter($filePath);
        }

        $f = fopen($filePath, 'r+');
        $result = [];
        while (($row = self::fgetcsvUTF8($f, 100000, $delimiter))) {
            $hasNotEmpty = false;
            foreach ($row as $value) {
                if (!empty($value)) {
                    $hasNotEmpty = true;
                    continue;
                }
            }
            if ($hasNotEmpty) {
                $result[] = $row;
            }
        }
        fclose($f);

        return $result;
    }
}
