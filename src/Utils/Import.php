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
use OZiTAG\Tager\Backend\Utils\Formatters\ExceptionFormatter;

class Import
{
    use JobDispatcherTrait;

    protected BaseImportStrategy $strategy;

    protected array $params;

    protected string $filePath;

    protected array $header;

    protected ?string $delimiter = null;

    protected ?array $fileData = null;

    public function setFile(string $filePath)
    {
        if (!is_file($filePath)) {
            throw new ImportNotFoundFileException('File not found');
        }

        $this->filePath = $filePath;
    }

    public function setStrategy(BaseImportStrategy $strategy, array $params = [])
    {
        $this->strategy = $strategy;

        $this->params = $params;
    }

    public function setDelimiter(?string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    protected function setHeader(array $header)
    {
        foreach ($header as &$item) {
            $item = trim($item);
        }

        $this->header = $header;
    }

    protected function prepareRow(array $row): array
    {
        $fields = $this->strategy->fields();

        $result = [];

        foreach ($this->header as $ind => $title) {
            foreach ($fields as $field => $label) {
                if ($title == $label) {
                    $result[$field] = isset($row[$ind]) && $row[$ind] ? trim($row[$ind]) : null;
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

        $rows = CsvReader::loadFromFile($this->filePath, $this->delimiter);
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
            $this->run($importJobClass, [
                'rows' => $rows,
                'params' => $this->params
            ]);
            return;
        }

        $importRowJobClass = $this->strategy->getImportRowJobClass();
        if (empty($importRowJobClass)) {
            throw new ImportException('Import Error - Import Row Job class is not set');
        }

        foreach ($rows as $ind => $row) {
            try {
                $result = $this->run($importRowJobClass, [
                    'row' => $row,
                    'params' => $this->params
                ]);

                if ($result !== true && !empty($result)) {
                    throw new ImportRowException('Import error - Row ' . ($ind + 1) . ' - ' . $result);
                }
            } catch (\Throwable $exception) {
                throw new ImportRowException('Import error - Row ' . ($ind + 1) . ' - ' .
                    ExceptionFormatter::getMessageWithFileInfo($exception));
            }
        }
    }
}
