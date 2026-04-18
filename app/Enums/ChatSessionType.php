<?php

namespace App\Enums;

enum ChatSessionType: string
{
    case User = 'user';
    case Guest = 'guest';
}
