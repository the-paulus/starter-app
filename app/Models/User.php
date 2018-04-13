<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
