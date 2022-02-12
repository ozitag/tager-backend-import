<?php

namespace OZiTAG\Tager\Backend\Import\Enums;

enum ImportSessionStatus: string
{
    case Created = 'CREATED';
    case Validation = 'VALIDATION';
    case InProgress = 'IN_PROGRESS';
    case Completed = 'COMPLETED';
    case Failure = 'FAILURE';

    public static function label(self $value): string
    {
        return match ($value) {
            self::Created => 'Создан',
            self::Validation => 'Валидация',
            self::InProgress => 'В процессе',
            self::Completed => 'Завершен',
            self::Failure => 'Ошибка',
            default => 'Unknown',
        };
    }
}
