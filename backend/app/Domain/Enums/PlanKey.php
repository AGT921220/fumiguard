<?php

namespace App\Domain\Enums;

enum PlanKey: string
{
    case BASIC = 'BASIC';
    case PRO = 'PRO';
    case ENTERPRISE = 'ENTERPRISE';
}

