<?php

namespace OZiTAG\Tager\Backend\Import\Operations;

use Carbon\Carbon;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Operation;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Jobs\RunImportSessionJob;
use OZiTAG\Tager\Backend\Import\Jobs\SetImportSessionParamsJob;
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
            'status' => ImportSessionStatus::Created->value,
            'created_at' => Carbon::now(),
        ]);

        $paramsFiltered = [];
        foreach ($this->request->params as $param) {
            $paramsFiltered[$param['name']] = $param['value'];
        }

        $this->run(SetImportSessionParamsJob::class, [
            'model' => $model,
            'params' => $paramsFiltered
        ]);

        $this->run(RunImportSessionJob::class, [
            'id' => $model->id,
            'delimiter' => $this->request->delimiter == "\\t" ? "\t" : $this->request->delimiter,
            'params' => $paramsFiltered
        ]);

        return $model;
    }
}
