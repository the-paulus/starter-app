<?php

use Illuminate\Support\Arr;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class SettingsTableSeeder extends DatabaseSeeder
{

    public static $settings = [
        'General' => [
            [
                'name' => 'notification_email',
                'description' => 'Email address to send application notifications to.',
                'setting_type' => 5,
                'value' => 'admin@starterapp.local',
                'weight' => 0,
            ],
            [
                'name' => 'email_from',
                'description' => 'Email address to send from.',
                'setting_type' => 5,
                'value' => 'admin@starterapp.local',
                'weight' => 1,
            ],
            [
                'name' => 'email_from_name',
                'description' => 'Name to use in the from field of emails sent out by the application.',
                'setting_type' => 7,
                'value' => 'Starter App',
                'weight' => 2,
            ],
            [
                'name' => 'email_body',
                'description' => 'Notification email body.',
                'setting_type' => 8,
                'value' => '',
                'weight' => 3,
            ],
        ],
    ];

    public static $setting_types = ['integer','ip','ip4','ip6','email','date','string','html','text'];

    public function run()
    {

        foreach(self::$setting_types as $setting_type) {

            \DB::table('setting_types')->insert(['name' => $setting_type]);

        }

        foreach(self::$settings as $group_name => $setting_group) {

            $new_group = SettingGroup::all()->firstWhere('name', '=', $group_name);
            print_r($new_group);

            foreach($setting_group as $setting) {

                try {

                    $new_setting = Setting::validateAndCreate($setting);
                    $new_group->settings()->save($new_setting);

                } catch(\Illuminate\Validation\ValidationException $validationException) {

                    $this->command->error(print_r(implode("\n",Arr::flatten($validationException->errors())), TRUE));
                    $this->command->info('Data: ' . print_r($setting, TRUE));

                }

            }

        }

    }
}
