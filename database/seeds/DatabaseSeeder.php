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
        User::class,
        UserGroup::class,
        Permission::class,
        Setting::class,
        SettingGroup::class,
    ];

    private $pivot_tables = [
        'user_user_group',
        'permission_user_group',
    ];

    private $seeders = [
        UserGroupsTableSeeder::class,
        UsersTableSeeder::class,
    ];

    public function startClean() {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach($this->pivot_tables as $pt) {

            DB::table($pt)->truncate();

        }

        foreach($this->model_tables as $mt) {

            $mt::truncate();

        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->seeders as $seeder) {

            $this->call($seeder);

        }
    }
}
