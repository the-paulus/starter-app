<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Scout\Searchable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends BaseModel implements Authenticatable, JWTSubject
{
    use Notifiable, SoftDeletes, Searchable;

    /**
     * @var bool Creates two timestamp fields created_at and updated_at. The default is TRUE
     */
    public $timestamps = TRUE;

    /**
     * The name of the create column name in the database.
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the updated column name in the database.
     */
    const UPDATED_AT = 'updated_at';

    /**
     * @var string The format of the timestamp when it is stored and retrieved.
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @var bool Instructs Scout that searches should be performed as the user types.
     */
    public $asYouType = true;

    /**
     * @var array Columns that have dates.
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var array The attributes that can be assigned en masse.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'auth_type'
    ];

    /**
     * @var array Additional attributes assigned to the model.
     */
    protected $appends = ['user_group_ids'];

    /**
     * @var array Relations that are eager-loaded when the model is retrieved from the database.
     */
    protected $with = ['groups'];

    /**
     * @var array How the data within the model should be. When a rule fails, the message with the same key as the rule will be
     * returned.
     */
    public static $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|dynamic_unique:users,email,{id}|email',
        'auth_type' => 'required|exists:auth_types,name',
        'password' => 'required_password:auth_type,local',
    ];

    /**
     * @var array Messages to use when a rule fails.
     */
    public static $messages = [
        'first_name' => 'First name is required.',
        'last_name' => 'Last name is required.',
        'email' => 'A valid email address is required.',
        'auth_type' => 'Select the type of authentication.',
        'password' => 'A password is required.'
    ];

/*    public static $relationshipRules = [
        'user_group_ids' => 'required_or_empty_array|array|exists:user_groups,id',
    ];

    public static $relationshipMessages = [
        'user_group_ids' => 'List of groups is required.'
    ];*/

    /**
     * @var array The attributes that should be visible for array.
     */
    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'email',
        'user_group_ids',
        'groups',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array The attributes that should be hidden.
     */
    protected $hidden = [
        'auth_type',
        'password',
        'remember_token',
    ];

    /**
     * Returns all groups a user belongs to as a BelongsToMany object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups() {

        return $this->belongsToMany(UserGroup::class);

    }

    /**
     * Returns all permissions a user has as a Collection.
     *
     * @return Collection
     */
    public function permissions() {

        $permissions = new Collection();

        foreach($this->groups()->get()->all() as $group) {

            foreach($group->permissions()->get()->all() as $permission) {

                $permissions->put($permission->id, $permission);

            }
        }

        return $permissions;

    }

    /**
     * Determine if user has provided $permission.
     *
     * @param integer|string|Permission $permission Permission to look for.
     * @return bool
     */
    public function hasPermission($permission) {

        if(is_integer($permission)) {

            $lookup_permission = $this->permissions()->firstWhere('id', '=', $permission);

        } else if(is_object($permission) && get_class($permission) == Permission::class) {

            $lookup_permission = $this->permissions()->firstWhere('id', '=', $permission->id);

        } else {

            $lookup_permission = $this->permissions()->firstWhere('name', '=', $permission);

        }

        return !is_null($lookup_permission);

    }

    /**
     * Determine if the user is a member of the specified $group.
     *
     * @param integer|string|UserGroup $group   Group to look for.
     * @return bool
     */
    public function memberOf($group) {

        if(is_integer($group)) {

            $lookup_group = $this->groups()->get()->firstWhere('id', '=', $group);

        } else if(is_object($group) && get_class($group) == UserGroup::class) {

            $lookup_group = $this->groups()->get()->firstWhere('id', '=', $group->id);

        } else {

            $lookup_group = $this->groups()->get()->firstWhere('name', '=', $group);

        }

        return !is_null($lookup_group);

    }

    /**
     * Authorization type mutator. Converts the name into an integer that is stored in the database.
     *
     * @param string    $auth_name  Name of the authorization method.
     */
    public function setAuthTypeAttribute($auth_name) {

        $this->attributes['auth_type'] = DB::table('auth_types')->where('name', '=', $auth_name)->value('id');

    }

    /**
     * Authorization type accessor. Returns the name of the authorization method based on the integer found in the
     * auth_type column.
     *
     * @return string|null  Name of the authorization method.
     */
    public function getAuthTypeAttribute() {

        return DB::table('auth_types')->where('id', '=', $this->attributes['auth_type'])->value('name');

    }

    /**
     * Mutates the password attribute by encrypting the value.
     *
     * @param $password string Plain text password to encrypt.
     */
    public function setPasswordAttribute($password) {

        $this->attributes['password'] = Hash::make($password);

    }

    /**
     * Accessor for user_group_ids attribute that returns the IDs of the groups the user belongs to.
     *
     * @return Collection
     */
    public function getUserGroupIdsAttribute() {

        return $this->groups()->allRelatedIds();

    }

    /**
     * Mutate the model's groups attribute.
     *
     * @param $ids  array   Array of group IDs that the user belongs to.
     */
    public function setUserGroupIdsAttribute($ids) {

        $this->groups()->sync($ids);

    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getJWTCustomClaims()
    {

        return ['auth_type' => $this->getAuthTypeAttribute()];

    }

    public function getJWTIdentifier()
    {

        return $this->getKey();

    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        unset($array['user_group_ids']);
        unset($array['auth_type']);
        unset($array['password']);

        return $array;
    }

    /**
     * Sets whether or not a particular module should be added to the index and searchable.
     *
     * @return bool Indicates whether or not model should be searchable.
     */
    public function shouldBeSearchable()
    {
        return true;
    }

}
