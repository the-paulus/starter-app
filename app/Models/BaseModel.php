<?php

namespace App\Models;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @var array Key value array where the key is the name of the field and the value is the rule.
     */
    public static $rules = [];

    /**
     * @var array Messages that should be used when validation fails.
     */
    public static $messages = [];

    /**
     * @var array Key value array where the key
     */
    public static $relationshipRules = [];

    /**
     * @var array Messages to use when a relationship rule fails.
     */
    public static $relationshipMessages = [];

    private $validators = [];

    /**
     * @var array Additional model permissions.
     */
    public static $permissions = [];

    /**
     * @var array Default model permissions.
     */
    protected static $model_permissions = [
        'manage',
        'create',
        'modify',
        'delete',
        'access'
    ];

    /**
     * Returns the name of the model class.
     *
     * @return string Name of the model class.
     */
    public static function baseModelClassName() {

        return substr(strrchr(get_called_class(), '\\'), 1);

    }

    /**
     * Returns a single array of permissions that is the merged elements of both the $permissions and $model_permissions arrays.
     *
     * @return array All permissions associated with the model.
     */
    public static function getModelPermissions() {

        $all_permissions = array();

        for($i = 0; $i < count(self::$model_permissions); $i++) {

            $all_permissions[] = self::getModelPermission(self::$model_permissions[$i]);

        }

        //$all_permissions = array_merge(self::$permissions, self::$model_permissions);

        return $all_permissions;

    }

    /**
     * Returns the model specific permission for managing, creating, modifying, deleting, or accessing data.
     *
     *
     * @param string $permission A string that is either manage, create, modify, delete, or access.
     *
     * @return string The value passed in with the name of the class appended or an empty string if the value of $permission is not valid.
     */
    public static function getModelPermission($permission) {

        return self::$model_permissions[array_search($permission, self::$model_permissions)] . ' ' . strtolower(self::baseModelClassName()) . 's';

    }

    /**
     * Validates the $data based on the rules in the referenced class.
     *
     * @param array $data Values that are to be validated.
     *
     * @throws ValidationException
     */
    public static function validate($data) {

        if( property_exists(self::class, 'rules') ) {

            $model_validator = Validator::make($data, static::$rules);
            $relationship_validator = Validator::make($data, static::$relationshipRules);

            if( $model_validator->fails() ) {

                throw new ValidationException($model_validator);
            }

            if( $relationship_validator->fails() ) {

                throw new ValidationException($relationship_validator);

            }
        }

        // TODO: Handle situations in which the trait is applied to classes other than models.

    }

    /**
     * Helper function that is used in static methods.
     *
     * @param array $data Values that are to be validated.
     *
     * @return $this
     *
     * @throws ValidationException
     */
    private function validateModel($data = []) {

        static::validate(array_merge($this->attributesToArray(), $data));

        return $this;
    }

    /**
     * Validates $data and creates a new model if the information provided follows the model's rules.
     *
     * @param array $data Values that is to be assigned to the model.
     *
     * @return Model
     *
     * @throws ValidationException
     */
    public static function validateAndCreate($data) {

        static::validate($data);

        return static::create($data);

    }

    /**
     * Validates $data and updates the model with the provided values.
     *
     * @param array $data Values that is to be assigned to the model.
     *
     * @return Model
     *
     * @throws ValidationException
     */
    public function validateAndUpdate(array $data) {

        $data = array_merge(self::attributesToArray(), $data);

        return $this->validateModel($data)->update($data);

    }

    /**
     * Returns the model object with updated relationships.
     *
     * @return Model
     */
    public function freshRelationships() {

        return $this->fresh($this->with);

    }
}
