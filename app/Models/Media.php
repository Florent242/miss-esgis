<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'miss_id',
        'url',
        'type',
        'date_upload',
    ];
    protected $casts = [
        'date_upload' => 'datetime'
    ];

    public $timestamps = false;


    public function miss()
    {
        return $this->belongsTo(Miss::class, 'miss_id');
    }

    public function getUrlAttribute($value)
    {
        // Retourner simplement la valeur stockée (juste le nom du fichier)
        return $value;
    }


}
