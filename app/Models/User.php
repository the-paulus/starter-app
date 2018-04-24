<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

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
     * @var array Columns that have dates.
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'auth_type'
    ];

    /**
     * How the data within the model should be.
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|unique:users,email|email',
        // TODO: Enable this to be dynamic so other methods can be added on the fly.
        'auth_type' => 'required|in:shibboleth,local',
        // TODO: This should be conditional depending on the type of authentication used.
        'password' => 'required_if:auth_type,local'
    ];

    /**
     * Messages to use when a rules fails.
     * @var array
     */
    public static $messages = [
        'first_name' => 'First name is required.',
        'last_name' => 'Last name is required.',
        'email' => 'A valid email address is required.',
        'auth_type' => 'Select the type of authentication.',
        'password' => 'A password is required.'
    ];

    /**
     * The attributes that should be visible for array.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'email',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
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
}
