<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Miss extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'age',
        'date_naissance',
        'pays',
        'telephone',
        'email',
        'bio',
        'photo_principale',
        'mot_de_passe',
        'statut',
        'date_inscription',
    ];
     public $timestamps = false;
    /**
     * Get the medias for the miss.
     */
    public function medias()
    {
        return $this->hasMany(Media::class, 'miss_id');
    }
      public function photos(): HasMany
    {
        return $this->hasMany(Media::class)->where('type','photo');
    }
     public function videos(): HasMany
    {
        return $this->hasMany(Media::class)->where('type','video');
    }

    /**
     * Get the votes for the miss.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class, 'miss_id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    // Accessor for Totalvote
    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }
}
