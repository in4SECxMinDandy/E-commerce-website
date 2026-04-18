<?php

namespace App\Enums;

enum VisitSessionStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Disabled = 'disabled';
}
