<?php

namespace App\Providers;

use http\Exception\InvalidArgumentException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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

            if($validator->validateRequired($attribute, $value) && ($validator->validateArray($attribute, $value) && $validator->validateFile($attribute, $value)) ) {

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

            $data = $validator->getData();
            $auth_type = false;

            if(isset($data['auth_type']) && !empty($data['auth_type'])) {

                $column = intval($data['auth_type']) ? 'id' : 'name';
                $auth_type = DB::table('auth_types')->select()->where($column, $data['auth_type'])->first();

                if(is_null($auth_type)) return false;

            }

            if(is_object($auth_type) && $auth_type->name == 'local') {

                return !empty($value) && is_string($value);

            }

            return ($auth_type && empty($value)) || (!$auth_type && empty($value));

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
     * The unique_email works exactly like the unique rule but is explicitly used for email fields that are defined as
     * unique in the database. This rule takes the parameters of table,column,uniqueColumn. By default the parameters
     * are users,email,id.
     */
    private function validateUniqueEmail() {

        Validator::extend('unique_email', function($attribute, $value, $parameters, $validator) {

            $data = $validator->getData();
            $id = (isset($data['id'])) ? $data['id'] : Auth::id();
            $parameters[0] = empty($parameters[0]) ? 'users' : $parameters[0];
            $parameters[1] = empty($parameters[1]) ? 'email' : $parameters[1];
            $parameters[2] = $id;
            $parameters[3] = !(isset($parameters[3])) ? 'id' : $parameters[3];

            return $validator->validateEmail($attribute, $value) && $validator->validateUnique($attribute, $value, $parameters);

        }, ':attribute must be unique.');
    }

    /**
     * Creates a new validation rule called 'required_with_permission' that requires a value for the field being validated
     * based on permissions. This takes one parameter, the string array of permissions a user has that would
     * require the field to be required. If a user has one of the listed permissions then the rule returns true.
     */
    private function validateRequiredWithPermission() {

        Validator::extendImplicit('required_with_permission', function($attribute, $value, $parameters, $validator) {

            foreach($parameters as $parameter) {

                if(Auth::user()->hasPermission($parameter)) {

                    return $validator->validateRequired($attribute, $value) && !empty($value);

                };

            }

            return true;

        }, ':attribute is required.');
    }

    /**
     * Creates a new validation rule called 'required_without_permission' that requires a value for the field being validated
     * based on the absence of a permission. This takes one parameter, the string array of permissions a that a
     * user doesn't have that would require the field to be required. If a user has one of the permissions the rule
     * returns true.
     */
    private function validateRequiredWithoutPermission() {

        Validator::extendImplicit('required_without_permission', function($attribute, $value, $parameters, $validator) {

            foreach($parameters as $parameter) {

                if(Auth::user()->hasPermission($parameter)) {

                    return true;

                }
            }

            return $validator->validateRequired($attribute, $value) && !empty($value);

        }, ':attribute is required. (without permission)');
    }

    /**
     * Creates a new validation rule called 'required_with_membership' that requires a value for the field being validated
     * based on group. This takes one parameter, the string array of groups a user could be in that would
     * require the field to be required.
     */
    private function validateRequiredWithMembership() {

        Validator::extendImplicit('required_with_membership', function($attribute, $value, $parameters, $validator) {

            foreach($parameters as $parameter) {

                if(Auth::user()->memberOf($parameter)) {

                    return $validator->validateRequired($attribute, $value);

                };

            }

            return true;

        }, ':attribute is required.');
    }

    /**
     * Creates a new validation rule called 'required_without_membership' that requires a value for the field being validated
     * based on group. This takes one parameter, the string array representation of groups a user that is not in that would
     * require the field to be required.
     */
    private function validateRequiredWithoutMembership() {

        Validator::extendImplicit('required_without_membership', function($attribute, $value, $parameters, $validator) {

            foreach($parameters as $parameter) {

                if((Auth::user()) && Auth::user()->memberOf($parameter)) {

                    return true;

                }
            }

            return $validator->validateRequired($attribute, $value) && !empty($value);

        }, ':attribute is required.');
    }

    /**
     * Creates a new validation rule called 'has_permission' that takes two parameters. The first is the callback
     * defined in the model that is used to validate the second parameter. Two possible choices for the first parameter
     * is 'hasPermission' or 'memberOf'. The second parameter is the permission or group name to verify the user has
     * permission to do edit the field being validated.
     */
    private function validateHasPermission() {

        Validator::extend('has_permission', function($attribute, $value, $parameters, $validator) {

            foreach($parameters as $parameter) {

                if(Auth::user()->hasPermission($parameter)) {

                    return true;

                }

            }

            return false;

        });

    }


}
