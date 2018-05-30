<?php

use App\Models\User;
use App\Models\UserGroup;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\SettingGroup;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private $model_tables = [
        UserGroup::class,
        User::class,
        Permission::class,
        SettingGroup::class,
        Setting::class,
    ];

    private $pivot_tables = [
        'user_user_group',
        'permission_user_group',
        'auth_types',
        'setting_types',
    ];

    private $seeders = [
        UserGroupsTableSeeder::class,
        UsersTableSeeder::class,
        SettingGroupsTableSeeder::class,
        SettingsTableSeeder::class,
        PermissionsTableSeeder::class,
    ];

    private $db_foreign_key_checks_on = [
        'mysql' => 'SET FOREIGN_KEY_CHECKS=1;',
        'sqlite' => 'PRAGMA foreign_keys = ON',
    ];

    private $db_foreign_key_checks_off = [
        'mysql' => 'SET FOREIGN_KEY_CHECKS=0;',
        'sqlite' => 'PRAGMA foreign_keys = OFF',
    ];

    public function startClean() {

        DB::statement($this->db_foreign_key_checks_off[env('DB_CONNECTION')]);

        foreach($this->pivot_tables as $pt) {

            DB::table($pt)->truncate();

        }

        foreach($this->model_tables as $mt) {

            $mt::truncate();

        }

        DB::statement($this->db_foreign_key_checks_on[env('DB_CONNECTION')]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->startClean();

        foreach($this->seeders as $seeder) {

            $this->call($seeder);

        }
    }
}
