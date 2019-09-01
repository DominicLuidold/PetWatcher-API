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
        'image',
    ];

    /**
     * Get the pets living in this home
     *
     * @return HasMany
     */
    public function pets()
    {
        return $this->hasMany('PetWatcher\Domain\Pet');
    }
}
