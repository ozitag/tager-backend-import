<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

use App\Tager\Import\ProductPrices\ProductPricesImportRowJob;
use App\Tager\Import\ProductPrices\ProductPricesRow;
use App\Tager\Import\ProductPrices\ProductPricesValidateRowJob;

abstract class BaseImportStrategy
{
    abstract function getId(): string;

    abstract function getName(): string;

    abstract function fields(): array;

    abstract function getRowClass(): string;

    abstract function getValidateRowJobClass(): string;

    abstract function getImportRowJobClass(): string;

    public function getCacheNamespaces(): array
    {
        return [];
    }
}
