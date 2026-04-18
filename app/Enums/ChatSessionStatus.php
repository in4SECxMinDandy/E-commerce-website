<?php

namespace App\Enums;

enum ChatSessionStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
