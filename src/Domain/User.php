<?php

declare(strict_types=1);

namespace PetWatcher\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static Model|Builder create($attributes = array())
 * @method static Model|Collection|static[]|static|null find($id, $columns = array())
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class User extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $dateFormat = 'U';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'displayName',
    ];

    /**
     * Get all homes owned by the user
     *
     * @return HasMany
     */
    public function homesOwned()
    {
        return $this->hasMany('PetWatcher\Domain\Home', 'owner');
    }

    /**
     * Get all homes accessible to the user
     */
    public function accessibleHomes()
    {
        return $this->belongsToMany('PetWatcher\Domain\Home');
    }
}
