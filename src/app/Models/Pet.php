<?php

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model {
    protected $dateFormat = 'U';
    protected $casts = [
        'dateOfBirth' => 'date',
    ];
    protected $fillable = [
        'name',
        'dateOfBirth',
        'weight',
        'location',
    ];

    public function home() {
        return $this->belongsTo('PetWatcher\Models\Home');
    }
}
