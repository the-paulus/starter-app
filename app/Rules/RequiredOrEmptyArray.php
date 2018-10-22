<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

/**
 * RequiredOrEmptyArray class defines the validation rule that checks an attribute's value to ensure that it contains a
 * value or is an empty array.
 *
 * @package App\Rules
 */
class RequiredOrEmptyArray implements ImplicitRule {
    use ValidatesAttributes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()  {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {

        return ( $this->validateRequired($attribute, $value) ) || ( ( is_array($value) || $value instanceof \Countable) && count($value) < 1 );

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {

        return ':attribute is required or needs to be an empty array.';

    }

}
