<?php

namespace OZiTAG\Tager\Backend\Import;

use OZiTAG\Tager\Backend\Import\Contracts\BaseImportStrategy;

class TagerImport
{
    private static array $strategies = [];

    private static ?string $fileScenario = null;

    public static function init(?string $fileScenario = null)
    {
        self::$fileScenario = $fileScenario;
    }

    public static function registerStrategy(string $stategyClassName)
    {
        /** @var BaseImportStrategy $strategy */
        $strategy = new $stategyClassName;

        if (is_subclass_of($stategyClassName, BaseImportStrategy::class) == false) {
            throw new \Exception($stategyClassName . ' is not a subclass of BaseImportStrategy');
        }

        self::$strategies[$strategy->getId()] = $strategy;
    }

    public static function getStrategy(string $strategyId): ?BaseImportStrategy
    {
        return self::$strategies[$strategyId] ?? null;
    }

    /**
     * @return BaseImportStrategy[]
     */
    public static function getStrategies(): array
    {
        return self::$strategies;
    }

    public static function getFileScenario(): string
    {
        return self::$fileScenario;
    }
}
