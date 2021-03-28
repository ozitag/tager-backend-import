<?php

namespace OZiTAG\Tager\Backend\Import\Features;

use Illuminate\Http\Resources\Json\JsonResource;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Core\Resources\SuccessResource;
use OZiTAG\Tager\Backend\Import\TagerImport;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportStrategiesFeature extends Feature
{
    public function handle()
    {
        $data = [];

        foreach (TagerImport::getStrategies() as $strategy) {
            $data[] = [
                'id' => $strategy->getId(),
                'name' => $strategy->getName()
            ];
        }

        return new JsonResource($data);
    }
}
