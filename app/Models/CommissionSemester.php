<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CommissionSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_id',
        'term_start_month',
        'term_start_year',
        'term_fees',
        'own_commission',
        'agent_commission',
        'reminder_date',
        'base_currency_rate'
    ];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {

        static::creating(function ($model) {
            $requestData = request()->all();
            $requestData = json_decode($requestData['components'][0]['snapshot'], true);
            $fees_currency = StudentAdmission::find($requestData['data']['data'][0]['admission_id'])->fees_currency;

            $model->base_currency_rate = CurrencyHelper::findBaseCurrencyRate($fees_currency);
        });
        static::updating(function ($model) {
            $requestData = request()->all();
            $requestData = json_decode($requestData['components'][0]['snapshot'], true);
            $fees_currency = StudentAdmission::find($model->commission->admission_id)->fees_currency;

            $model->base_currency_rate = CurrencyHelper::findBaseCurrencyRate($fees_currency);
        });
    }

    /**
     * Belongs-to relationship with Commission.
     */
    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
}
