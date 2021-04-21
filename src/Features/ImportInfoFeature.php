<?php

namespace OZiTAG\Tager\Backend\Import\Features;

use Illuminate\Http\Resources\Json\JsonResource;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Core\Resources\SuccessResource;
use OZiTAG\Tager\Backend\Import\Resources\ImportInfoResource;
use OZiTAG\Tager\Backend\Import\TagerImport;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportInfoFeature extends Feature
{
    public function handle()
    {
        $result = new ImportInfoResource([]);

        $result->setStrategies(TagerImport::getStrategies());
        $result->setFileScenario(config('tager-import.fileScenario'));

        return $result;
    }
}
