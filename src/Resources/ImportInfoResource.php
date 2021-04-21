<?php

namespace OZiTAG\Tager\Backend\Import\Resources;

use OZiTAG\Tager\Backend\Core\Resources\JsonResource;
use OZiTAG\Tager\Backend\Import\Contracts\BaseImportStrategy;
use OZiTAG\Tager\Backend\Import\TagerImport;

class ImportInfoResource extends JsonResource
{
    protected ?string $fileScenario;

    /** @var BaseImportStrategy[] */
    protected array $strategies;

    public function setStrategies(array $value)
    {
        $this->strategies = $value;
    }

    public function setFileScenario(?string $value)
    {
        $this->fileScenario = $value;
    }

    public function toResponse($request)
    {
        $result = [
            'fileScenario' => $this->fileScenario,
            'strategies' => []
        ];

        foreach ($this->strategies as $strategy) {
            $result['strategies'][] = [
                'id' => $strategy->getId(),
                'name' => $strategy->getName()
            ];
        }

        return $result;
    }
}
