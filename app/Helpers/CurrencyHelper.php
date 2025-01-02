<?php

namespace App\Helpers;

use App\Models\Currency;
use NumberFormatter;

class CurrencyHelper
{
    /**
     * Convert amount from one currency to another
     *
     * @param float $amount The amount to convert
     * @param string $fromCurrency The source currency
     * @param string $toCurrency The target currency
     * @return float The converted amount
     */
    public static function convert($amount, $fromCurrency, $toCurrency, $baseCurrencyRate = '')
    {
        if (empty($amount) || $amount == 0) {
            return 0;
        }
        // Get base currency rates
        $fromCurrencyRate = empty($baseCurrencyRate) ? Currency::where('currency', $fromCurrency)->first()->base_currency_rate : $baseCurrencyRate;
        // Handle if any currency doesn't have a rate (error checking)
        if (!$fromCurrencyRate) {
            throw new \Exception("Currency rate not found for {$fromCurrency} or {$toCurrency}");
        }

        // Convert the amount to INR (base currency)
        $amountInBaseCurrency = $amount * $fromCurrencyRate;

        // Convert from INR to the target currency
        return $amountInBaseCurrency;
    }

    /**
     * Get the base currency rate for the given currency
     *
     * @param string $currency The currency to get the rate for
     * @return float The base currency rate for the given currency
     */
    public static function findBaseCurrencyRate($currency)
    {
        return Currency::where('currency', $currency)->first()->base_currency_rate ?? 1;
    }

    public static function formatAmount($amount)
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        if ($amount >= 1000) {
            return $formatter->format($amount / 1000) . 'K';
        }
        return $formatter->format($amount);
    }
}
