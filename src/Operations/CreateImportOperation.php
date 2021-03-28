<?php

namespace OZiTAG\Tager\Backend\Import\Operations;

use Carbon\Carbon;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Operation;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;
use OZiTAG\Tager\Backend\Import\Requests\ImportStoreRequest;

class CreateImportOperation extends Operation
{
    protected ImportStoreRequest $request;

    public function __construct(ImportStoreRequest $request)
    {
        $this->request = $request;
    }

    public function handle(ImportSessionRepository $repository)
    {
        $model = $repository->fillAndSave([
            'strategy' => $this->request->strategy,
            'file_id' => Storage::fromUUIDtoId($this->request->file),
            'status' => ImportSessionStatus::Created,
            'created_at' => Carbon::now(),
        ]);

        return $model;
    }
}
