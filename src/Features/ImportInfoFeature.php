<?php

namespace OZiTAG\Tager\Backend\Import\Features;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Import\Resources\ImportInfoResource;
use OZiTAG\Tager\Backend\Import\TagerImport;

class ImportInfoFeature extends Feature
{
    public function handle()
    {
        $result = new ImportInfoResource([]);

        $result->setStrategies(TagerImport::getStrategies());
        $result->setFileScenario(TagerImport::getFileScenario());

        return $result;
    }
}
