<?php

namespace App\Enums;

enum Status: string
{
    case Synced = 'synced';
    case Failed = 'failed';
}
