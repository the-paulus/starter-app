<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * UserGroup class contains information about user groups and has the ids for permissions and users associated with it.
 *
 * @package App\Models
 */
class UserGroup extends BaseModel {

    use Searchable, SoftDeletes;

    /**
     * The name of the create column name in the database.
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the updated column name in the database.
     */
    const UPDATED_AT = 'updated_at';

    /**
     * How the data within the model should be.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique_exclude_current:user_groups,name',
        'description' => 'required',
    ];

    /**
     * Messages to use when a rules fails.
     *
     * @var array
     */
    public static $messages = [
        'name' => 'Group name is required and must be unique.',
        'description' => 'Description is required.',
    ];

    /**
     * @var bool Creates two timestamp fields created_at and updated_at. The
     *   default is TRUE
     */
    public $timestamps = true;

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
        'deleted_at',
    ];

    /**
     * @var bool Instructs Scout that searches should be performed as the user types.
     */
    public $asYouType = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * @var array An array of strings representing the name of a "virtual" column in the database.
     */
    protected $appends = [
        'user_ids',
        'permission_ids'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be visible for array.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'description',
        'user_ids',
        'permission_ids',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Returns all users in the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {

        return $this->belongsToMany(User::class);
        
    }

    /**
     * Returns all permissions in the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {

        return $this->belongsToMany(Permission::class);

    }

    /**
     * Determine whether or not the group has a member of the specified $user.
     *
     * @param integer|string|UserGroup $user   User to look for.
     * @return bool
     */
    public function hasMember($user) {

        $lookup_user = null;

        if(is_integer($user)) {

            $lookup_user = $this->users()->get()->firstWhere('id', '=', $user);

        } else if(is_object($user) && get_class($user) == User::class) {

            $lookup_user = $this->users()->get()->firstWhere('id', '=', $user->id);

        } else {

            // TODO: Use search functionality.
            //$lookup_group = $this->groups()->firstWhere('name', '=', $user);

        }

        return !is_null($lookup_user);

    }

    /**
     * Accessor for user_ids attribute that returns a Collection of user IDs that are in the group.
     *
     * @return Collection
     */
    public function getUserIdsAttribute() {

        return $this->users()->allRelatedIds();

    }

    /**
     * Mutate the model's users attribute.
     *
     * @param $ids  array   Array of users IDs that are in the group.
     */
    public function setUserIdsAttribute($ids) {

        $this->users()->sync($ids);

    }

    /**
     * Accessor for permission_ids attribute that returns a Collection of permission IDs that are assigned to the group.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionIdsAttribute() {

        return $this->permissions()->allRelatedIds();

    }

    /**
     * Mutate the model's permissions attribute.
     *
     * @param $ids  array   Array of permission IDs that are assigned to the group.
     */
    public function setPermissionIdsAttribute($ids) {

        $this->permissions()->sync($ids);

    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray() {

        $arr = $this->toArray();

        unset($arr['user_ids']);
        unset($arr['permission_ids']);

        return $arr;

    }

    /**
     * Sets whether or not a particular module should be added to the index and searchable.
     *
     * @return bool Indicates whether or not model should be searchable.
     */
    public function shouldBeSearchable() {

        return true;

    }

}
