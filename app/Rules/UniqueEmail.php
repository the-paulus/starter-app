<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

/**
 * UniqueEmail class defines the validation rule that checks an attribute's value to ensure that it contains an email
 * and is unique to only the id associated with it.
 *
 * @package App\Rules
 */
class UniqueEmail implements ImplicitRuleRule
{
    use ValidatesAttributes;

    /**
     * @var string Table that the value should be unique.
     */
    private $table = 'users';

    /**
     * @var string Field that should be unique.
     */
    private $field = 'email';

    /**
     * @var int ID of the user.
     */
    private $id = 0;

    /**
     * @var string Name of the ID field.
     */
    private $id_field = 'id';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

        if(func_get_arg(0)) {

            $this->table = func_get_arg(0);

        }

        if(func_get_arg(1)) {

            $this->field = func_get_arg(1);

        }

        if(func_get_arg(2)) {

            $this->id = is_numeric(func_get_arg(2)) ? func_get_arg(2) : Auth::id();

        }

        if(func_get_arg(3)) {

            $this->id_field = func_get_arg(3);

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

        return $this->validateEmail($attribute, $value)
            && $this->validateUnique($attribute, $value, [$this->table, $this->field, $this->id, $this->id_field]);

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Email must be unique.';
    }
}
