<?php

declare(strict_types=1);

namespace PetWatcher\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property string $name
 * @property string $image
 * @property mixed  $home
 *
 * @method static Model|Builder|static create($attributes = array())
 * @method static Model|Collection|static[]|static|null find($id, $columns = array())
 */
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
     * Get the home this pet lives in.
     *
     * @return BelongsTo
     */
    public function home(): BelongsTo
    {
        return $this->belongsTo('PetWatcher\Domain\Home', 'home');
    }
}
