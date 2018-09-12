<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\File;
use DB;
use Illuminate\Support\Facades\Validator;
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

            if($validator->validateRequired($attribute, $value) && $validator->validateArray($attribute, $value)) {

                return true;

            }

            if( $validator->validateFile($attribute, $value) && $validator->validateArray($attribute, $value) ) {

                return true;

            }

            if( $validator->validateArray($attribute, $value) ){

                return true;

            }

            return false;

        }, ":attribute is required to be a populated array or empty array.");

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

        Validator::extendImplicit('exists_in', function($attribute, $value, $parameters, $validator) {

            $table = $parameters[0];
            $column = isset($parameters[1]) ? $parameters[1] : 'name';

            return DB::table($table)->select()->where($column, $value)->exists();

        }, ':value is not valid for :attribute');

    }

    /**
     * The dynamic_unique works exactly like the unique rule but allows the developer to pass a place holder for the
     * ID that gets replaced by the value returned from Auth::id(). This rule is intended for use with attributes such as
     * email addresses, so users can update their own account without validation failing because their email address isn't
     * unique.
     */
    private function validateDynamicUnique() {

        Validator::extend('dynamic_unique', function($attribute, $value, $parameters, $validator) {

            if (isset($parameters[2]) ){

                $parameters[2] = str_replace('{id}', Auth::id(), $parameters[2]);

            }

            return $validator->validateUnique($attribute, $value, $parameters);

        }, ':attribute must be unique.');
    }

    /**
     * Creates a new validatoin rule called 'permission_required' that requires a value for the field being validated
     * based on permissions or group membership. This takes two parameters, the first is the callback, which is either
     * 'hasPermission' or 'memberOf'. The second parameter takes either a permission or group name, respectively.
     */
    private function validatePermissionRequired() {

        Validator::extend('permission_required', function($attribute, $value, $parameters, $validator) {

            $negate = trim($parameters[1]);

            if(strcmp('true', $negate) === 0) {
                return !Auth::user()->hasPermission($parameters[0]);
            }

            return Auth::user()->hasPermission($parameters[0]);

        }, ':attribute permission is required.');
    }

    /**
     * Creates a new validation rule called 'has_permission' that takes two parameters. The first is the callback
     * defined in the model that is used to validate the second parameter. Two possible choices for the first parameter
     * is 'hasPermission' or 'memberOf'. The second parameter is the permission or group name to verify the user has
     * permission to do edit the field being validated.
     */
    private function validateHasPermission() {

        Validator::extend('has_permission', function($attribute, $value, $parameters, $validator) {

            if(!is_null(Auth::user()) && count($parameters)) {

                return Auth::user()->{$parameters[0]}($parameters[1]);

            }

            return false;

        });

    }


}
