<?php

namespace App\Models;

// use ArrayAccess;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

trait DefaultVirtualModel {

    /**
     * Creates a new VirtualModel.
     *
     * @param array     $data   Attributes of the new VirtualModel
     *
     * @return VirtualModel
     */
    public static function create(array $data) {

        return new static($data);

    }

    /**
     * Saves the data to a remote resource.
     *
     * @param array     $data   Attributes of the new VirtualModel
     *
     * @return bool
     */
    public function save(array $data) {

        return TRUE;

    }

    /**
     * Destroys the model from the remote resource.
     *
     * @param mixed $ids    Identifier or identifiers of resources that should be deleted.
     *
     * @return bool
     */
    public function destroy($ids) {

        return TRUE;

    }

}

/**
 * VirtualModel class works the same way as a normal Eloquent Model class but instead of interacting the database it
 * can act on data from other sources such as from web apis.
 * @package App\Models
 */
class VirtualModel {

    /**
     * @var array The attributes that can be assigned en masse.
     */
    protected $fillable = [];

    /**
     * @var array The attributes of the model.
     */
    public $attributes = [];

    /**
     * @var array How the data within the model should be. When a rule fails, the message with the same key as the rule will be
     * returned.
     */
    public static $rules = [];

    /**
     * @var array Messages to use when a rule fails.
     */
    public static $messages = [];

    /**
     * VirtualModel constructor.
     *
     * @param array $data
     */
    public function __construct($data = []) {

        $this->fill($data);

    }

    /**
     * Returns the name of the mutator method for a an attribute.
     *
     * @param string|int    $key        Integer or string representation of the key for a desired
     * @param string        $prefix     String to prepend to the name of the mutator method.
     * @return string
     */
    protected function getMutatorName($key, $prefix = '') {

        return $prefix . str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $key)))) . 'Attribute';

    }

    /**
     * Fills in the the attributes of the model with a key-value array provided as an argument.
     *
     * @param array $data
     */
    public function fill(array $data) {

        // NOTE: we only support fillable, not guarded/etc or wildcards
        foreach ($data as $key => $value) {

            if (in_array($key, $this->fillable)) {

                $this->setAttribute($key, $value);

            }

        }

    }

    /**
     * Returns the value of the specified attribute.
     *
     * Called by the magic __get and __set, so the virtual model can have the $model->attribute behavior Eloquent does.
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key) {

        $getter = $this->getMutatorName($key, 'get');

        if (method_exists($this, $getter)) {

            return $this->$getter();

        } else {

            return $this->attributes[$key];

        }

    }

    /**
     * Sets the value of an attribute specified by $key.
     *
     * @param string    $key   Name of the attribute to set.
     * @param mixed     $value  Value to set.
     */
    public function setAttribute($key, $value) {

        $setter = $this->getMutatorName($key, 'set');

        if (method_exists($this, $setter)) {

            $this->$setter($value);

        } else {

            $this->attributes[$key] = $value;

        }

    }

    /**
     * Creates a new VirtualModel. This needs to be defined by the developer either manually coding the methods or by
     * using the DefaultVirtualModel trait.
     *
     * @param array     $data   Attributes of the new VirtualModel
     *
     * @return VirtualModel
     */
    abstract public static function create(array $data);

    /**
     * Saves the data to a remote resource.
     *
     * @param array     $data   Attributes of the new VirtualModel
     *
     * @return bool
     */
    abstract public static function save(array $data);

    /**
     * Destroys the model from the remote resource.
     *
     * @param mixed $ids    Identifier or identifiers of resources that should be deleted.
     *
     * @return bool
     */
    abstract public static function destroy($ids);

    /**
     * Validates the models attributes.
     *
     * @throws ValidationException
     */
    public function validate() {

        $validator = Validator::make($this->attributes, static::$rules);

        if ($validator->fails()) {

            $validator->setCustomMessages(['model' => static::class]);

            throw new ValidationException($validator);

        }

    }

    /**
     * Validates and creates a new VirtualModel.
     *
     * @param array     $data   Attributes of the new model.
     *
     * @return VirtualModel
     */
    public static function validateAndCreate(array $data) {

        // NOTE: Since the model isn't being stored, we can just create and then validate that instance.
        $instance = new static($data);
        $instance->validate();

        return $instance;

    }

    /**
     * @var array An array of methods that aren't declared static but need to be called statically.
     */
    protected static $callAsStaticMethods = ['validate'];

    /**
     * Calls non-static methods as static.
     *
     * @param string $name      name of method to call.
     * @param array $arguments  Array of arguments that will be passed to the method call.
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        if (in_array($name, static::$callAsStaticMethods)) {

            $attributes = array_shift($arguments);
            $instance = new self($attributes);

            return $instance->{$name}(...$arguments); // splat is supported in PHP5.6+
        } else {

            return parent::__call($name, $arguments);

        }

    }

    // TODO: json functions?

    public function __get($key) {

        return $this->getAttribute($key);

    }

    public function __set($key, $value) {

        $this->setAttribute($key, $value);

    }

}