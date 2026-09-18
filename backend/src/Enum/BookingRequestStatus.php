<?php

namespace App\Enum;

enum BookingRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Declined = 'declined';
    case Cancelled = 'cancelled';
}
