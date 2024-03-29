<?php

namespace PetWatcher\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $token
 *
 * @method static Model|Builder|static create($attributes = array())
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class Token extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $dateFormat = 'U';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'user_id',
        'token',
        'validThru',
    ];
}
