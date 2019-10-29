<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RequiredWithPermission implements Rule
{
    use ValidatesAttributes;

    /**
     * @var array Array of group names.
     */
    private $permissions = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

        if( func_num_args() < 1 ) {

            throw new \InvalidArgumentException();

        } else if( func_num_args() == 1) {

            if( !is_array(func_get_arg(1)) ) {

                $this->permissions[0] = func_get_arg(1);

            } else {

                $this->permissions = func_get_arg(1);

            }

        } else {

            $this->permissions = func_get_args();

        }

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {

        foreach($this->permissions as $permission) {

            if(Auth::user()->isMemberOf($permission)) {

                return $this->validateRequired($attribute, $value);

            }

        }

        return TRUE;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {

        return ':attribute is required.';

    }
}