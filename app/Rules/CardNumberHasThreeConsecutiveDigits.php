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

        // stores the last checked character in the string (value)
        $last_character = '';

        // how many consecutive same digits we have, starts at 0 but is immediately set to 1 after the first character is checked
        $number_of_consecutive_same_digits = 0;

        // pass trough each character in the  string (value)
        for ($i=0; $i < strlen($value); $i++) {
            // set initial last_character as current for the beginning of the string
            if ($i === 0) {
                $last_character = $value[$i];
            }

            // increment on success
            if ($value[$i] === $last_character) {
                $number_of_consecutive_same_digits++;
            }

            // reset to 1 on failure
            if ($value[$i] !== $last_character) {
                $number_of_consecutive_same_digits = 1;
            }

            // update for the next iteration
            $last_character = $value[$i];

            // stop the pass trough when condition is met
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
        return 'Person\'s card number should have tree consecutive equal digits';
    }
}
