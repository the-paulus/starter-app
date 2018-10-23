<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
/**
 * ExistsEnum class defines the validation rule that checks an attribute's value to ensure that it contains a
 * value defined in an enum column.
 *
 * @package App\Rules
 */
class ExistsEnum implements Rule
{
    /**
     * @var string The table that contains the enum field.
     */
    private $table;

    /**
     * @var string The name of the enum field.
     */
    private $field;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

        if( func_num_args() != 2 ) {

            throw new \InvalidArgumentException();

        }

        $this->table = func_get_arg(0);
        $this->field = func_get_arg(1);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value) {

        $enums = DB::select( raw("SHOW COLUMNS FROM :table WHERE Field = :column"), [':table' => $this->table, ':column' => $this->field]);

        preg_match("/^enum\(\'(.*)\'\)$/", $enums, $matches);

        $enums = explode("','", $matches[1]);

        return in_array($value, $enums);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {

        return 'Invalid selection.';

    }
}
