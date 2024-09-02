<?php

namespace App\Enums;

enum PaySlipStatus: int
{
    case DRAFT = 0;
    case GENERATED = 1;
    case PAID = 2;
}