<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSandbox extends Model
{
    protected $table = 'payment_sandbox';

    protected $fillable = [
        'reference',
        'miss_id',
        'operator',
        'phone_number',
        'amount',
        'vote_count',
        'status',
        'momo_number',
        'sms_content',
        'sms_received_at',
        'ip_address',
        'user_agent',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'sms_received_at' => 'datetime',
    ];

    public function miss()
    {
        return $this->belongsTo(Miss::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPending()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}
