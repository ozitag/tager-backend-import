<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

use App\Tager\Import\ProductPrices\ProductPricesImportRowJob;
use App\Tager\Import\ProductPrices\ProductPricesRow;
use App\Tager\Import\ProductPrices\ProductPricesValidateRowJob;
use OZiTAG\Tager\Backend\Fields\Base\Field;

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

    /** @return Field[] */
    public function conditionalFields(): array
    {
        return [];
    }
}
