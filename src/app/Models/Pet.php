<?php
declare(strict_types=1);

namespace PetWatcher\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $dateFormat = 'U';

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'dateOfBirth' => 'date',
    ];

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name',
        'dateOfBirth',
        'weight',
        'location',
        'image',
    ];

    /**
     * Get the home of this pet
     *
     * @return BelongsTo
     */
    public function home()
    {
        return $this->belongsTo('PetWatcher\Models\Home');
    }
}
