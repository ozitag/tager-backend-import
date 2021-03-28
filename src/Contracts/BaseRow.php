<?php

namespace OZiTAG\Tager\Backend\Import\Contracts;

abstract class BaseRow
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }
}
