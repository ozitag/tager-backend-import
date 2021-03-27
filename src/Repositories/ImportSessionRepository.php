<?php

namespace OZiTAG\Tager\Backend\Import\Repositories;

use Illuminate\Support\Facades\DB;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Export\Models\ExportSession;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;

class ImportSessionRepository extends EloquentRepository
{
    public function __construct(ImportSession $model)
    {
        parent::__construct($model);
    }
}
