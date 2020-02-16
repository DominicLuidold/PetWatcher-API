<?php

declare(strict_types=1);

namespace PetWatcher\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $username
 * @property string $password
 * @property string $image
 * @property int    $admin
 *
 * @method static Model|Builder|static create($attributes = array())
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
        'image',
        'admin',
    ];

    /**
     * Get all homes owned by the user.
     *
     * @return HasMany
     */
    public function homesOwned(): HasMany
    {
        return $this->hasMany('PetWatcher\Domain\Home', 'owner');
    }

    /**
     * Get all homes accessible to the user.
     *
     * @return BelongsToMany
     */
    public function accessibleHomes(): BelongsToMany
    {
        return $this->belongsToMany('PetWatcher\Domain\Home');
    }
}
