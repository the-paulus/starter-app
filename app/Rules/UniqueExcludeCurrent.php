<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * UniqueExcludeCurrent class defines the validation rule that works almost exactly as the 'unique' rule with the
 * exception that the third parameter is substituted for the current model's id.
 *
 * @package App\Rules
 */
class UniqueExcludeCurrent implements Rule
{


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {

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
