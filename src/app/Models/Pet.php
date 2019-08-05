<?php
declare(strict_types=1);

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
        'image',
    ];

    public function home() {
        return $this->belongsTo('PetWatcher\Models\Home');
    }
}
