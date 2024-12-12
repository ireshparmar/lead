<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // Existing currencies
            ['currency_unit' => '1 CAD', 'currency' => 'CAD', 'base_currency_rate' => 60, 'base_currency' => 'INR'],
            ['currency_unit' => '1 AUD', 'currency' => 'AUD', 'base_currency_rate' => 56, 'base_currency' => 'INR'],
            ['currency_unit' => '1 USD', 'currency' => 'USD', 'base_currency_rate' => 72, 'base_currency' => 'INR'],
            ['currency_unit' => '1 MYR', 'currency' => 'MYR', 'base_currency_rate' => 17.6, 'base_currency' => 'INR'],
            ['currency_unit' => '1 AFN', 'currency' => 'AFN', 'base_currency_rate' => 60, 'base_currency' => 'INR'],
            ['currency_unit' => '1 EUR', 'currency' => 'EUR', 'base_currency_rate' => 160, 'base_currency' => 'INR'],

            // European currencies
            ['currency_unit' => '1 GBP', 'currency' => 'GBP', 'base_currency_rate' => 90, 'base_currency' => 'INR'], // British Pound
            ['currency_unit' => '1 CHF', 'currency' => 'CHF', 'base_currency_rate' => 80, 'base_currency' => 'INR'], // Swiss Franc
            ['currency_unit' => '1 SEK', 'currency' => 'SEK', 'base_currency_rate' => 7.5, 'base_currency' => 'INR'],  // Swedish Krona
            ['currency_unit' => '1 NOK', 'currency' => 'NOK', 'base_currency_rate' => 8, 'base_currency' => 'INR'],   // Norwegian Krone
            ['currency_unit' => '1 DKK', 'currency' => 'DKK', 'base_currency_rate' => 10.5, 'base_currency' => 'INR'], // Danish Krone
            ['currency_unit' => '1 PLN', 'currency' => 'PLN', 'base_currency_rate' => 16, 'base_currency' => 'INR'],  // Polish Zloty
            ['currency_unit' => '1 HUF', 'currency' => 'HUF', 'base_currency_rate' => 0.22, 'base_currency' => 'INR'], // Hungarian Forint
            ['currency_unit' => '1 CZK', 'currency' => 'CZK', 'base_currency_rate' => 3.2, 'base_currency' => 'INR'],  // Czech Koruna
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['currency' => $currency['currency']], $currency);
        }
    }
}
