<?php

namespace App\Http\Controllers;

use App\Models\SettingGroup;

class SettingGroupController extends Controller
{
    protected static $model = SettingGroup::class;
    
    /**
     * @var string $default_sort Name of the field to sort on by default.
     */
    protected static $default_sort = 'name';

    /**
     * @var string Default sorting order. Either ASC or DESC.
     */
    protected static $default_order = 'ASC';
}
