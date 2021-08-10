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

    public function getData()
    {
        $result = [
            'fileScenario' => $this->fileScenario,
            'strategies' => []
        ];

        foreach ($this->strategies as $strategy) {
            $fields = [];

            foreach ($strategy->conditionalFields() as $name => $conditionalField) {
                $fields[] = $conditionalField->setName($name)->getJson();
            }

            $result['strategies'][] = [
                'id' => $strategy->getId(),
                'name' => $strategy->getName(),
                'fields' => $fields
            ];
        }

        return $result;
    }
}
