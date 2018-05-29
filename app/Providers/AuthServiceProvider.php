<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\User;
use App\Models\UserGroup;
use App\Policies\PermissionPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserGroupPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        UserGroup::class => UserGroupPolicy::class,
        Setting::class => SettingPolicy::class,
        SettingGroup::class => SettingGroupPolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
