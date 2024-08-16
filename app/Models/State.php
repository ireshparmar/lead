<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'country_id',
        'country_code',
        'iso2',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
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
            $builder->whereIn('country_code', $specificCountries)->whereHas('country', function ($q) {
                $q->where('status', 'Active');
            })->orderBy('country_code');
        });
    }
}
