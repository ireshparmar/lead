<?php

namespace App\Rules;

use App\Models\VisaType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function PHPUnit\Framework\isNull;

class checkVisaType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $type  = VisaType::where('name', $value)->count();
        if($type == 0) {
            $fail('The :attribute is invalid.');
        }
    }
}
