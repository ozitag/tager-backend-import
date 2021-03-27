<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

abstract class BaseImportStrategy
{
    abstract function getId(): string;

    abstract function getName(): string;
}
