<?php

use Illuminate\Support\Arr;
use App\Models\UserGroup;
use Illuminate\Validation\ValidationException;

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

        UserGroup::unguard(true);

        foreach(self::$groups as $group) {

            try {

                UserGroup::validateAndCreate($group);

                $this->command->info($group['name'] . ' created successfully.');

            } catch(ValidationException $validationException) {

                $this->command->error('Unable to create ' . $group['name']);
                $this->command->error(implode("\n", Arr::flatten($validationException->errors())));

            }

        }

        UserGroup::unguard(false);
    }
}
