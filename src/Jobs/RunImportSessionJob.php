<?php

namespace OZiTAG\Tager\Backend\Import\Jobs;

use Carbon\Carbon;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Core\Jobs\QueueJob;
use OZiTAG\Tager\Backend\Import\Jobs\SetImportSessionStatusJob;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;

class RunImportSessionJob extends QueueJob
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(ImportSessionRepository $importSessionRepository)
    {
        /** @var ImportSession $model */
        $model = $importSessionRepository->find($this->id);
        if (!$model) {
            return;
        }

        dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::InProgress));

        try {

        } catch (\Exception $exception) {
            dispatch(new SetImportSessionStatusJob($model, ImportSessionStatus::Failure, $exception->getMessage()));
        }
    }
}
