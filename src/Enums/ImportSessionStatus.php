<?php

namespace OZiTAG\Tager\Backend\Import\Enums;

enum ImportSessionStatus: string
{
    case Created = 'CREATED';
    case Validation = 'VALIDATION';
    case InProgress = 'IN_PROGRESS';
    case Completed = 'COMPLETED';
    case Failure = 'FAILURE';

    public static function label(string $value): string
    {
        return match ($value) {
            self::Created->value => 'Создан',
            self::Validation->value => 'Валидация',
            self::InProgress->value => 'В процессе',
            self::Completed->value => 'Завершен',
            self::Failure->value => 'Ошибка',
            default => 'Unknown',
        };
    }
}
