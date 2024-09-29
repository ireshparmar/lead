<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'country_id',
        'state_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('specificCountries', function (Builder $builder) {
            $specificCountries = [
                'AU',
                'CA',
                'HR',
                'FI',
                'DE',
                'HU',
                'LV',
                'LT',
                'PL',
                'RU',
                'IN'
            ];
            $builder->whereIn('country_code', $specificCountries)->whereHas('state', function ($q) {
                $q->where('status', 'Active');
            })->whereHas('country', function ($q) {
                $q->where('status', 'Active');
            });
        });
    }
}
