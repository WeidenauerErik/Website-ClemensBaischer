<?php

namespace App\Enum;

enum BookingType: string
{
    case Customer = 'customer';
    case Blocked = 'blocked';
}
