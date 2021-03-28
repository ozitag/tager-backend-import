<?php

namespace OZiTAG\Tager\Backend\Import\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\QueueJob;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;
use OZiTAG\Tager\Backend\Import\Utils\Import;

class RunImportSessionJob extends QueueJob
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(ImportSessionRepository $importSessionRepository, Import $import)
    {
        /** @var ImportSession $model */
        $model = $importSessionRepository->find($this->id);
        if (!$model) {
            return;
        }

        try {
            $import->setFile($model->file->getPath(), true);
            $import->setStrategy($model->strategy);

            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Validation));
            $import->validate();

            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::InProgress));
            $import->process();
        } catch (\Exception $exception) {
            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Failure, $exception->getMessage()));
        }
    }
}
