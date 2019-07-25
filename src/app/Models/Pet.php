<?php

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model {
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $fillable = [
        'name',
        'dateOfBirth',
        'weight',
        'location',
        'home_id',
    ];

    public function home() {
        return $this->belongsTo('PetWatcher\Models\Home');
    }
}
