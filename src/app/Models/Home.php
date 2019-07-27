<?php

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model {
    protected $dateFormat = 'U';
    protected $fillable = [
        'name',
    ];

    public function pets() {
        return $this->hasMany('PetWatcher\Models\Pet');
    }
}
