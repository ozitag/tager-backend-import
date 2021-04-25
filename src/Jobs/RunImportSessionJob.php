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

class RunImportSessionJob extends QueueJob
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(ImportSessionRepository $importSessionRepository, Import $import, HttpCache $httpCache)
    {
        /** @var ImportSession $model */
        $model = $importSessionRepository->find($this->id);
        if (!$model) {
            return;
        }

        try {
            $import->setFile($model->file->getPath());

            $strategy = TagerImport::getStrategy($model->strategy);
            if (!$strategy) {
                throw new ImportNotFoundStrategyException('Strategy "' . $model->strategy . '" not found');
            }
            $import->setStrategy($strategy);

            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Validation));
            $import->validate();

            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::InProgress));
            $import->process();

            if (!empty($strategy->getCacheNamespaces())) {
                $httpCache->clear($strategy->getCacheNamespaces());
            }

            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Completed));
        } catch (\Exception $exception) {
            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Failure, $exception->getMessage()));
        }
    }
}
