<?php
declare(strict_types=1);

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model {
    protected $dateFormat = 'U';
    protected $fillable = [
        'name',
        'image',
    ];

    public function pets() {
        return $this->hasMany('PetWatcher\Models\Pet');
    }
}
