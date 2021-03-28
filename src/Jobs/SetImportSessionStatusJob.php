<?php

namespace OZiTAG\Tager\Backend\Import\Jobs;

use Carbon\Carbon;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Core\Jobs\QueueJob;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;

class SetImportSessionStatusJob extends Job
{
    protected ImportSession $importSession;

    protected string $status;

    protected ?string $message;

    public function __construct(ImportSession $importSession, string $status, ?string $message = null)
    {
        $this->importSession = $importSession;

        $this->status = $status;

        $this->message = $message;
    }

    public function handle(ImportSessionRepository $importSessionRepository)
    {
        $importSessionRepository->set($this->importSession);

        if ($this->status == ImportSessionStatus::Validation) {
            $importSessionRepository->fillAndSave([
                'status' => ImportSessionStatus::Validation,
                'started_at' => Carbon::now()
            ]);
        } else if ($this->status == ImportSessionStatus::InProgress) {
            $importSessionRepository->fillAndSave([
                'status' => ImportSessionStatus::InProgress,
                'validated_at' => Carbon::now()
            ]);
        } else if ($this->status == ImportSessionStatus::Completed) {
            $importSessionRepository->fillAndSave([
                'status' => ImportSessionStatus::Completed,
                'completed_at' => Carbon::now(),
                'message' => $this->message
            ]);
        } else if ($this->status == ImportSessionStatus::Failure) {
            $importSessionRepository->fillAndSave([
                'status' => ImportSessionStatus::Failure,
                'completed_at' => Carbon::now(),
                'message' => $this->message
            ]);
        }
    }
}
