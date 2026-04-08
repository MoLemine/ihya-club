<?php

namespace App\Enums;

enum BloodRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';
    case Archived = 'archived';
}
