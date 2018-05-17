<?php

namespace App\Models;

class UserGroup extends BaseModel
{

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
        'name' => 'required|unique:user_groups,name',
    ];

    /**
     * Messages to use when a rules fails.
     *
     * @var array
     */
    public static $messages = [
        'name' => 'Group name is required and must be unique.',
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    protected $appends = ['user_ids'];

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

        return $this->users()->get('id');

    }

    /**
     * Mutate the model's users attribute.
     *
     * @param $ids  array   Array of users IDs that are in the group.
     */
    public function setUserIdsAttribute($ids) {

        $this->users()->sync($ids);

    }
}
