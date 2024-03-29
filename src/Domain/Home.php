<?php

declare(strict_types=1);

namespace PetWatcher\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property mixed  $owner
 * @property string $image
 *
 * @method static Model|Builder|static create($attributes = array())
 * @method static Model|Collection|static[]|static|null find($id, $columns = array())
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class Home extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $dateFormat = 'U';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'name',
        'owner',
        'image',
    ];

    /**
     * Get the owner of this home.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo('PetWatcher\Models\User', 'owner');
    }

    /**
     * Get all users that can access this home.
     *
     * @return BelongsToMany
     */
    public function accessors(): BelongsToMany
    {
        return $this->belongsToMany('PetWatcher\Models\User');
    }

    /**
     * Get all pets living in this home.
     *
     * @return HasMany
     */
    public function pets(): HasMany
    {
        return $this->hasMany('PetWatcher\Domain\Pet', 'home');
    }
}
