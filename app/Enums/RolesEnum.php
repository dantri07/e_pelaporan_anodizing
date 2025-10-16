<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case OPERATOR = 'operator';
    case TECHNICIAN = 'technician';

    public function label(): string
    {
        return match ($this) {
            static::ADMIN => 'Admin',
            static::OPERATOR => 'Operator',
            static::TECHNICIAN => 'Technician',
        };
    }
}
