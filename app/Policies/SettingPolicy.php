<?php

namespace App\Policies;

use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy extends ApplicationPolicy
{

    static protected $model = Setting::class;

}
