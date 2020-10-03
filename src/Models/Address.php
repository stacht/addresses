<?php

namespace Stacht\Addresses\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'label',
        'given_name',
        'family_name',
        'organization',
        'country_code',
        'address1',
        'address2',
        'state',
        'city',
        'postal_code',
        'phone',
        'latitude',
        'longitude',
        'is_primary',
        'is_warehouse',
        'is_billing',
        'is_shipping',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saved(function ($model) {
    //         if ($model->is_primary === true) {
    //             $this->addressable()->whereNot('id', $model->id)->get()->each(function($row){
    //                 $row->is_primary = false;
    //                 $row->save();
    //             })
    //         }
    //     });
    // }

    /*
     * The default rules that the model will validate against.
     *
     * @var array
     */
    public static function rules(): array
    {
        return [
            'addressable_id' => 'required|integer',
            'addressable_type' => 'required|string|max:150',
            'label' => 'nullable|string|max:150',
            'given_name' => 'required|string|max:150',
            'family_name' => 'nullable|string|max:150',
            'organization' => 'nullable|string|max:150',
            'country_code' => 'nullable|alpha|size:2',
            'address1' => 'required|string|max:150',
            'address2' => 'nullable|string|max:150',
            'state' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:150',
            'phone' => 'nullable|string',
            'latitude' => 'nullable|float',
            'longitude' => 'nullable|float',
            'is_primary' => 'sometimes|boolean',
            'is_warehouse' => 'sometimes|boolean',
            'is_billing' => 'sometimes|boolean',
            'is_shipping' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the owner model of the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo('addressable', 'addressable_type', 'addressable_id');
    }

    /**
     * Scope primary addresses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPrimary(Builder $builder): Builder
    {
        return $builder->where('is_primary', true);
    }

    /**
     * Scope primary addresses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsWarehouse(Builder $builder): Builder
    {
        return $builder->where('is_warehouse', true);
    }

    /**
     * Scope billing addresses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsBilling(Builder $builder): Builder
    {
        return $builder->where('is_billing', true);
    }
    /**
     * Scope shipping addresses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsShipping(Builder $builder): Builder
    {
        return $builder->where('is_shipping', true);
    }

    /**
     * Scope addresses by the given country.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string                                $countryCode
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCountry(Builder $builder, string $countryCode): Builder
    {
        return $builder->where('country_code', $countryCode);
    }

    /**
     * Get full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return implode(' ', [$this->given_name, $this->family_name]);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $address) {
            if (config('stacht.addressable.geocode')) {
                $segments[] = $address->street;
                $segments[] = sprintf('%s, %s %s', $address->city, $address->state, $address->postal_code);
                // $segments[] = country($address->country_code)->getName();
                $query = str_replace(' ', '+', implode(', ', $segments));
                $geocode = json_decode(file_get_contents("https://maps.google.com/maps/api/geocode/json?address={$query}&sensor=false"));
                if (\count($geocode->results)) {
                    $address->latitude = $geocode->results[0]->geometry->location->lat;
                    $address->longitude = $geocode->results[0]->geometry->location->lng;
                }
            }
        });
    }
}
