<?php

namespace OZiTAG\Tager\Backend\Import\Utils;

use Illuminate\Foundation\Bus\DispatchesJobs;
use OZiTAG\Tager\Backend\Core\Traits\JobDispatcherTrait;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportException;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportLoadFileException;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportNotFoundFileException;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportNotFoundStrategyException;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportRowException;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportValidationException;
use OZiTAG\Tager\Backend\Import\Contracts\BaseImportStrategy;
use OZiTAG\Tager\Backend\Import\TagerImport;

class Import
{
    use JobDispatcherTrait;

    protected BaseImportStrategy $strategy;

    protected string $filePath;

    protected array $header;

    protected bool $isWindows1251;

    protected ?array $fileData = null;

    public function setFile(string $filePath, bool $isWindows1251 = true)
    {
        if (!is_file($filePath)) {
            throw new ImportNotFoundFileException('File not found');
        }

        $this->isWindows1251 = $isWindows1251;

        $this->filePath = $filePath;
    }

    public function setStrategy(BaseImportStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    protected function setHeader(array $header)
    {
        $this->header = $header;
    }

    protected function prepareRow(array $row): array
    {
        $fields = $this->strategy->fields();

        $result = [];

        foreach ($this->header as $ind => $title) {
            foreach ($fields as $field => $label) {
                if ($title == $label) {
                    $result[$field] = $row[$ind];
                    break;
                }
            }
        }

        return $result;
    }

    private function loadFile(): array
    {
        if ($this->fileData !== null) {
            return $this->fileData;
        }

        $rows = CsvReader::loadFromFile($this->filePath, $this->isWindows1251);
        if ($rows == null) {
            throw new ImportLoadFileException('Load file error');
        }

        $this->setHeader($rows[0]);
        array_shift($rows);

        $rowClass = $this->strategy->getRowClass();
        $result = [];
        foreach ($rows as $item) {
            $result[] = new $rowClass(
                $this->prepareRow($item)
            );
        }

        $this->fileData = $result;

        return $result;
    }

    public function validate(): void
    {
        $rows = $this->loadFile();

        foreach ($rows as $ind => $row) {
            $result = $this->run($this->strategy->getValidateRowJobClass(), [
                'row' => $row
            ]);

            if ($result !== true && !empty($result)) {
                throw new ImportValidationException('Validation error - Row ' . ($ind + 1) . ' - ' . $result);
            }
        }
    }

    public function process(): void
    {
        $rows = $this->loadFile();

        $importJobClass = $this->strategy->getImportJobClass();
        if (!empty($importJobClass)) {
            $result = $this->run($importJobClass, [
                'rows' => $rows
            ]);
            return;
        }

        $importRowJobClass = $this->strategy->getImportRowJobClass();
        if (empty($importRowJobClass)) {
            throw new ImportException('Import Error - Import Row Job class is not set');
        }

        foreach ($rows as $ind => $row) {
            $result = $this->run($importRowJobClass, [
                'row' => $row
            ]);

            if ($result !== true && !empty($result)) {
                throw new ImportRowException('Import error - Row ' . ($ind + 1) . ' - ' . $result);
            }
        }
    }
}
