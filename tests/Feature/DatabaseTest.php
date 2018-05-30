<?php

namespace Tests\Feature;

use App\Models\SettingGroup;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use UsersTableSeeder;
use UserGroupsTableSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DatabaseTest extends TestCase
{

    /**
     * @group database
     * @group seeder
     * @group users
     * @group usergroups
     */
    public function testUserAndGroupsTableSeeders()
    {
        Artisan::call('db:seed', ['--class' => 'UserGroupsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'UsersTableSeeder']);

        $groups = UserGroup::all();
        $users = User::all();

        foreach ($groups as $gid => $group) {

            $this->assertEquals(UserGroupsTableSeeder::$groups[$gid]['name'], $group->name);
            $this->assertEquals(UserGroupsTableSeeder::$groups[$gid]['description'], $group->description);

        }

        foreach($users as $uid => $user) {

            foreach(UsersTableSeeder::$users[$uid] as $key => $val) {

                if($key != 'password') {

                    $this->assertEquals($val, $user->getAttribute($key));
                    $this->assertTrue($user->memberOf(UserGroupsTableSeeder::$groups[$uid]['name']));

                }

            }

        }
    }

    /**
     * @group database
     * @group seeder
     * @group settings
     * @group settinggroups
     */
    public function testSettingsAndGroupsTableSeeders() {

        Artisan::call('db:seed', ['--class' => 'SettingGroupsTableSeeder']);

        $groups = SettingGroup::all();
        $settings = Setting::all();

        foreach($groups as $gid => $group) {

            $this->assertEquals(\SettingGroupsTableSeeder::$settings_groups[$gid]['name'], $group->name);
            $this->assertEquals(\SettingGroupsTableSeeder::$settings_groups[$gid]['description'], $group->description);

        }

        foreach($settings as $sid => $setting) {

            foreach(\SettingsTableSeeder::$settings[$sid] as $key => $val) {

                $this->assertEquals($val, $setting->getAttribute($key));

            }
        }

    }

    public function testPermissionsTableSeeder() {

        Artisan::call('db:seed', ['--class' => 'PermissionsTableSeeder']);

    }
}