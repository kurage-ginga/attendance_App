<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use HasFactory;

    public function stampCorrection()
    {
        return $this->belongsTo(StampCorrection::class);
    }

    public function approver() // approved_by に対応
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
