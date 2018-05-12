<?php

namespace App\Models;

class Setting extends BaseModel
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
        'name' => 'required|unique:settings,name',
        'setting_type' => 'required|exists:setting_types,id',
        'weight' => 'integer',
    ];

    /**
     * Messages to use when a rules fails.
     *
     * @var array
     */
    public static $messages = [
        'name' => 'Group name is required and must be unique.',
        'setting_type' => 'Setting type is required.',
        'weight' => 'Weight must be an integer.',
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
    protected $fillable = [
        'name',
        'description',
        'setting_type',
        'value',
        'weight'
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
        'name',
        'description',
        'setting_type',
        'value',
        'weight'
    ];

    /**
     * Returns the group the setting belongs to as a BelongsTo relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group() {

        return $this->belongsTo(SettingGroup::class, 'setting_group_id','id');

    }
}
