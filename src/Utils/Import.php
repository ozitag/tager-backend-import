<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

use OZiTAG\Tager\Backend\Export\Exceptions\ExportNotFoundStrategyException;

use OZiTAG\Tager\Backend\Export\Exceptions\ImportLoadFileException;
use OZiTAG\Tager\Backend\Export\Exceptions\ImportNotFoundFileException;
use OZiTAG\Tager\Backend\Export\Exceptions\ImportValidationException;
use OZiTAG\Tager\Backend\Import\Contracts\BaseImportStrategy;
use OZiTAG\Tager\Backend\Import\TagerImport;

class Import
{
    private function getStrategy(string $strategyId): BaseImportStrategy
    {
        $strategy = TagerImport::getStrategy($strategyId);
        if (!$strategy) {
            throw new ExportNotFoundStrategyException('Strategy "' . $strategyId . '" not found');
        }

        return $strategy;
    }

    private function loadFile(string $filePath): array
    {
        if (!is_file($filePath)) {
            throw new ImportNotFoundFileException('File not found');
        }

        $rows = CsvReader::loadFromFile($filePath);
        if ($rows == null) {
            throw new ImportLoadFileException('Load file error');
        }

        return $rows;
    }

    public function validate(string $strategyId, string $filePath)
    {
        $strategy = $this->getStrategy($strategyId);
        $rows = $this->loadFile($filePath);

        if (!$strategy->validate($rows)) {
            throw new ImportValidationException('Validation error - ' . $strategy->getValidationError());
        }
    }

    public function run(string $strategyId, string $filePath)
    {
        $strategy = $this->getStrategy($strategyId);
        $rows = $this->loadFile($filePath);

        $strategy->run($rows);
    }
}
