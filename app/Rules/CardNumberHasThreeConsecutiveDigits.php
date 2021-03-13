<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CardNumberHasThreeConsecutiveDigits implements Rule
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
        if (empty($value)) {
            return false;
        }

        // need to make it a string so we can access $value[$i];
        $value = strval($value);

        $last_character = '';
        $number_of_consecutive_same_digits = 0;
        for ($i=0; $i < strlen($value); $i++) {
            if ($i === 0) {
                $last_character = $value[$i];
            }

            if ($value[$i] === $last_character) {
                $number_of_consecutive_same_digits++;
            }

            if ($value[$i] !== $last_character) {
                $number_of_consecutive_same_digits = 1;
            }

            $last_character = $value[$i];

            if ($number_of_consecutive_same_digits == 3) {
                return true;
            }
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
        return 'The validation error message.';
    }
}
