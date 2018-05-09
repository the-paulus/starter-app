<?php

use App\Models\User;
use App\Models\UserGroup;

use \Illuminate\Validation\ValidationException;

class UsersTableSeeder extends DatabaseSeeder
{

    private $users = [
        [
            'first_name' => 'Application',
            'last_name' => 'Admin',
            'email' => 'admin@starter-app.local',
            'password' => '',
            'auth_type' => 'local',
        ],
        [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin-user@starter-app.local',
            'password' => '',
            'auth_type' => 'local',
        ],
        [
            'first_name' => 'Standard',
            'last_name' => 'User',
            'email' => 'user@starter-app.local',
            'password' => '',
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

        foreach($this->users as $key => $value) {

            try {

                $user = User::validateAndCreate($value);

                $user->groups()->attache(UserGroup::all(['id'])->where('name', '=', UserGroupsTableSeeder::$groups[$key])->id);

                $this->command->info('Created ' . $value['first_name'] . ' ' . $value['last_name'] . '; added to ' . UserGroupsTableSeeder::$groups[$key]['name'] . ' group.');

            } catch(ValidationException $validationException) {

                $this->command->info($validationException->getMessage());

            }
        }
    }
}
