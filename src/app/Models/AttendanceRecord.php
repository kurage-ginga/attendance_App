<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'checkin_at',
        'checkout_at',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case 'original':
                return '修正なし';
            case 'pending_correction':
                return '修正申請中';
            case 'corrected':
                return '修正申請済み';
            default:
                return '不明';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function stampCorrections()
    {
        return $this->hasMany(StampCorrection::class);
    }
}
