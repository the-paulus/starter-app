<?php

namespace App\Models;

class SettingGroup extends BaseModel
{
    protected $table = 'setting_groups';

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
        'name' => 'required|min:1|max:12|unique:setting_groups,name',
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

    protected $appends = ['settings'];

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
        'settings',
    ];

    /**
     * Returns all settings a SettingGroup has as a HasMany Relationship object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function settings() {

        return $this->hasMany(Setting::class);

    }

    /**
     * Accessor for settings attribute that returns the settings that belong to the SettingGroup.
     *
     * @return Collection
     */
    public function getSettingsAttribute() {

        return $this->settings()->orderBy('weight')->get();

    }
}
