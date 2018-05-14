<?php

namespace App\Providers;

use Symfony\Component\HttpFoundation\File\File;
use DB;
use Validator;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class AppValidationProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $class_methods = get_class_methods($this);
        array_walk($class_methods, array($this, 'walkArrayValidators'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function walkArrayValidators(&$value, $key) {

        if( strpos($value, 'validate', 0) === 0 ) {

            try {

                $this->$value();


            } catch(\BadMethodCallException $badMethodCallException) {

                Log::error($value . ' was not found.');

            }

        }

    }

    /**
     * Creates a custom validation rule called 'required_or_empty_array' that requires the field in question to be
     * present and an array.
     *
     * This rule takes no parameters as it does a simple check to ensure that the field was found and is either an empty
     * array or one that contains elements.
     *
     * Note: This might be possible using a combination of sometimes, present, required, and array rules. However, having
     * this function will allow me to add more complex checking on the array such as ensuring there are no empty elements.
     * @see https://laravel.com/docs/5.5/validation#available-validation-rules
     */
    private function validateRequiredOrEmptyArray() {

        Validator::extendImplicit('required_or_empty_array', function($attribute, $value, $parameters, $validator) {

            if( $value instanceof File  && !empty($value->getPath()) ) {

                return false;

            }

            return !(is_null($value)) &&
                ( ( is_array($value) && count($value) == 0 ) || !( is_string($value) && empty($value) ) );

        }, ":attribute is required.");
    }


    /**
     * Creates a custom validation rule called 'required_password' that requires a value for the specified attribute
     * based on another attributes value.
     */
    private function validateRequiredPassword() {

        Validator::extendImplicit('required_password', function($attribute, $value, $parameters, $validator) {

            if( count($parameters) != 2 ) {

                throw new InvalidParameterException('Rule requires two parameters; attribute and value.');
            }

            // We do not tolerate exceptions!
            try {

            $data = $validator->getData();

            list($dependency, $requirement) = $parameters;

            return $data[$dependency] == $requirement && !empty($value);

            } catch(\Exception $exception) {

                return false;

            }

        }, ":attribute is required.");

    }

    /**
     * TODO: Remove or convert to exists_enum to verify that the value in question is enumerated in the specified tabled
     * or column.
     */
    private function validateExistsIn() {

        Validator::extend('exists_in', function($attribute, $value, $parameters, $validator) {

            $table = $parameters[0];
            $column = isset($parameters[1]) ? $parameters[1] : 'name';

            return DB::table($table)->select()->where($column, $value)->exists();

        }, ':value is not valid for :attribute');

    }

}
