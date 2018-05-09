<?php

use App\Models\UserGroup;

class UserGroupsTableSeeder extends DatabaseSeeder
{
    public static $groups = [
        [
            'name' => 'Application Administrator',
            'description' => 'Any person who needs to administer the application.',
        ],
        [
            'name' => 'Administrator',
            'description' => 'Any person who needs to perform administrative functions such as adding users.',
        ],
        [
            'name' => 'User',
            'description' => 'Any person who uses the application.',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::startClean();

        UserGroup::unguard(true);

        foreach($this->groups as $group) {

            try {

                UserGroup::validateAndCreate($group);

            } catch(\Dotenv\Exception\ValidationException $validationException) {

                $this->command->error('Unable to create ' . $group['name']);

            }

            $this->command->info($group['name'] . ' created successfully.');

        }

        UserGroup::unguard(false);
    }
}
