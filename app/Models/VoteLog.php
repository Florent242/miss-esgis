<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    protected $fillable = [
        'vote_id',
        'old_miss_id',
        'new_miss_id',
        'admin_id',
        'original_vote_time',
        'redirected_at',
        'ip_address',
        'user_agent'
    ];

    public $timestamps = false;

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function oldMiss()
    {
        return $this->belongsTo(Miss::class, 'old_miss_id');
    }

    public function newMiss()
    {
        return $this->belongsTo(Miss::class, 'new_miss_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
