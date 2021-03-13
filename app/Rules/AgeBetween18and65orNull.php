<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class AgeBetween18and65orNull implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // check for nulls
        if (!isset($value)) {
            return true;
        }

        // build the age from date time string
        $processed_value = str_replace(array('\\', '/'), '-', $value);
        $age = Carbon::parse($processed_value)->age;

        // check the age against the requirement
        if ($age >= 18 && $age <= 65) {
            return true;
        }

        // default is false
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Person\'s age should be between 18 and 65 or be Unknown';
    }
}
