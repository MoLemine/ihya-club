<?php

namespace App\Models;

use App\Enums\BloodRequestStatus;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'patient_name',
        'hospital_name',
        'city',
        'needed_on',
        'urgency_level',
        'required_units',
        'fulfilled_units',
        'description',
        'blood_type',
        'image_path',
        'status',
        'approved_at',
        'completed_at',
        'archived_at',
        'expires_at',
        'share_count',
    ];

    protected function casts(): array
    {
        return [
            'urgency_level' => UrgencyLevel::class,
            'status' => BloodRequestStatus::class,
            'approved_at' => 'datetime',
            'needed_on' => 'date',
            'completed_at' => 'datetime',
            'archived_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function scopeFeed($query)
    {
        return $query
            ->whereIn('status', [BloodRequestStatus::Approved->value, BloodRequestStatus::Completed->value])
            ->orderByRaw("CASE WHEN urgency_level = 'urgent' THEN 0 ELSE 1 END")
            ->latest();
    }
}
