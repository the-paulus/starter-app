<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

/**
 * RequiredPassword class defines the validation rule that checks a password value to ensure that it is present based
 * on the third and subsequent parameters depending on the third.
 *
 * @package App\Rules
 */
class RequiredPassword implements ImplicitRule {

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
    public function passes($attribute, $value)
    {
        //
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
