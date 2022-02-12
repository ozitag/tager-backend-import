<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

abstract class BaseImportStrategy
{
    abstract function getId(): string;

    abstract function getName(): string;

    abstract function fields(): array;

    abstract function getRowClass(): string;

    public function getValidateRowJobClass(): ?string
    {
        return null;
    }

    public function getImportJobClass(): ?string
    {
        return null;
    }

    public function getImportRowJobClass(): ?string
    {
        return null;
    }

    public function getCacheNamespaces(): array
    {
        return [];
    }

    public function conditionalFields(): array
    {
        return [];
    }
}
