<?php
declare(strict_types=1);

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Home extends Model {
    /**
     * {@inheritDoc}
     */
    protected $dateFormat = 'U';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name',
        'image',
    ];

    /**
     * Get the pets living in this home
     *
     * @return HasMany
     */
    public function pets() {
        return $this->hasMany('PetWatcher\Models\Pet');
    }
}
