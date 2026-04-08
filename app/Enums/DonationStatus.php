<?php

namespace App\Enums;

enum DonationStatus: string
{
    case Cart = 'cart';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
