<?php

namespace App\Policies;

use App\Models\SettingGroup;

class SettingGroupPolicy extends ApplicationPolicy
{
    static protected $model = SettingGroup::class;
}
