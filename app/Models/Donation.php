<?php

namespace App\Models;

use App\Enums\DonationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'user_id',
        'status',
        'cancellation_reason',
        'shared_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DonationStatus::class,
            'shared_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
