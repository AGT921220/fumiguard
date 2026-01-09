<?php

namespace App\Domain\Enums;

enum UserRole: string
{
    case TENANT_ADMIN = 'TENANT_ADMIN';
    case DISPATCHER = 'DISPATCHER';
    case TECHNICIAN = 'TECHNICIAN';
    case CLIENT_VIEWER = 'CLIENT_VIEWER';
}

