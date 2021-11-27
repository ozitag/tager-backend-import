<?php

namespace OZiTAG\Tager\Backend\Import\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\QueueJob;
use OZiTAG\Tager\Backend\HttpCache\HttpCache;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Exceptions\ImportNotFoundStrategyException;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;
use OZiTAG\Tager\Backend\Import\TagerImport;
use OZiTAG\Tager\Backend\Import\Utils\Import;
use OZiTAG\Tager\Backend\Utils\Formatters\ExceptionFormatter;

class SetImportSessionParamsJob extends QueueJob
{
    protected ImportSession $model;

    protected ?array $params;

    public function __construct(ImportSession $model, ?array $params = [])
    {
        $this->model = $model;

        $this->params = $params;
    }

    public function handle(ImportSessionRepository $importSessionRepository)
    {
        $strategy = TagerImport::getStrategy($this->model->strategy);
        if (!$strategy) {
            throw new ImportNotFoundStrategyException('Strategy "' . $model->strategy . '" not found');
        }

        $paramValues = [];

        $strategyFields = $strategy->conditionalFields();
        if (!empty($strategyFields)) {
            foreach ($strategyFields as $name => $strategyField) {
                $value = null;

                if (isset($this->params[$name])) {
                    $type = $strategyField->getTypeInstance();
                    $type->setValue($this->params[$name]);
                    $value = $type->getPublicValue();
                }

                $paramValues[] = ['label' => $strategyField->getLabel(), 'value' => $value];
            }
        }

        $importSessionRepository->set($this->model)->fillAndSave([
            'params' => empty($paramValues) ? null : json_encode($paramValues)
        ]);
    }
}
