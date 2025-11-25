<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'miss_id',
        'transaction_id',
        'moyen_paiement',
        'montant',
        'is_redirected',
        'intended_miss_id'
    ];
    public $timestamps = false;

    /**
     * Get the miss that received the vote.
     */
    public function miss()
    {
        return $this->belongsTo(Miss::class, 'miss_id');
    }

    /**
     * Get the transaction associated with the vote.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
