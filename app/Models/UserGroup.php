<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
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
        'name' => 'required|min:1|max:12|unique:user_groups,name',
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
        'name',
        'description',
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
}
