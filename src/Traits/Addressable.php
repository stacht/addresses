<?php

namespace Statch\Addresses\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait Addressable
{
    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param \Closure|string $callback
     */
    abstract public static function deleted($callback);

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param string $related
     * @param string $name
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Boot the addressable trait for the model.
     */
    public static function bootAddressable()
    {
        static::deleted(function (self $model) {
            $model->addresses()->delete();
        });
    }

    /**
     * Get all attached addresses to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(\Statch\Addresses\Models\Address::class, 'addressable');
    }

}
