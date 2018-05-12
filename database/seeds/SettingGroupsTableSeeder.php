<?php

use Illuminate\Support\Arr;
use App\Models\SettingGroup;

use Illuminate\Validation\ValidationException;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class SettingGroupsTableSeeder extends DatabaseSeeder
{
    public static $settings_groups = [
        [
            'name' => 'General',
            'description' => 'Non-specific application settings.',
        ],
    ];

    public function run()
    {

        foreach(self::$settings_groups as $group) {

            try {

                SettingGroup::validateAndCreate($group);

                $this->command->info($group['name'] . ' created successfully.');

            } catch(ValidationException $validationException) {

                $this->command->error('Unable to create ' . $group['name']);
                $this->command->error(implode("\n", Arr::flatten($validationException->errors())));

            }

        }
    }
}
