<?php

namespace App\Enums;

enum GroupStatusEnum:int{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case ARCHIVE = 2;
}
