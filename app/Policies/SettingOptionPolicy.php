<?php

namespace App\Policies;

use App\Models\User;
use App\SettingOption;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingOptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the settingOption.
     *
     * @param  \App\Models\User  $user
     * @param  \App\SettingOption  $settingOption
     * @return mixed
     */
    public function view(User $user, SettingOption $settingOption)
    {
        //
    }

    /**
     * Determine whether the user can create settingOptions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the settingOption.
     *
     * @param  \App\Models\User  $user
     * @param  \App\SettingOption  $settingOption
     * @return mixed
     */
    public function update(User $user, SettingOption $settingOption)
    {
        //
    }

    /**
     * Determine whether the user can delete the settingOption.
     *
     * @param  \App\Models\User  $user
     * @param  \App\SettingOption  $settingOption
     * @return mixed
     */
    public function delete(User $user, SettingOption $settingOption)
    {
        //
    }
}
