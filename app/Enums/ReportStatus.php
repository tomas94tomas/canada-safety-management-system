<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum ReportStatus: int
{
    use EnumToArray;
    case LOCAL = 1;
    case MIGRATED = 2;
    case PROCESSED = 3;
    case CANCELLED = 4;
}
