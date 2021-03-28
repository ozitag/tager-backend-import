<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

abstract class BaseImportStrategy
{
    abstract function getId(): string;

    abstract function getName(): string;

    abstract function fields(): array;

    abstract function validate(array $rows): bool;

    abstract function run(array $rows);

    protected ?string $validationError = null;

    protected function setValidationError(string $validationError)
    {
        $this->validationError = $validationError;
    }

    public function getValidationError(): ?string
    {
        return $this->validationError;
    }
}
