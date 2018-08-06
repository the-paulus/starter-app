<?php

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Arr;

use Illuminate\Validation\ValidationException;

class UsersTableSeeder extends DatabaseSeeder
{

    public static $users = [
        [
            'first_name' => 'Application',
            'last_name' => 'Admin',
            'email' => 'admin@starter-app.local',
            'password' => 'adminpasssecret',
            'auth_type' => 'local',
        ],
        [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin-user@starter-app.local',
            'password' => 'adminsecretagentuser',
            'auth_type' => 'local',
        ],
        [
            'first_name' => 'Standard',
            'last_name' => 'User',
            'email' => 'user@starter-app.local',
            'password' => 'justastandardpassword',
            'auth_type' => 'local',
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('auth_types')->insert([
            [ 'name' => 'local' ],
            [ 'name' => 'shibboleth' ]
        ]);

        foreach(self::$users as $key => $value) {

                $user = new User;
                $user->fill($value);
                $user->save();
                $user_group = UserGroup::all()->where('name', '=', UserGroupsTableSeeder::$groups[$key]['name'])->first();

                $user->groups()->attach($user_group);

                $this->command->info('Created ' . $value['first_name'] . ' ' . $value['last_name'] . '; added to ' . UserGroupsTableSeeder::$groups[$key]['name'] . ' group.');
        }
    }
}
