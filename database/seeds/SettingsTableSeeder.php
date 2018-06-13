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
                'setting_type' => 'email',
                'value' => 'admin@starterapp.local',
                'weight' => 0,
            ],
            [
                'name' => 'email_from',
                'description' => 'Email address to send from.',
                'setting_type' => 'email',
                'value' => 'admin@starterapp.local',
                'weight' => 1,
            ],
            [
                'name' => 'email_from_name',
                'description' => 'Name to use in the from field of emails sent out by the application.',
                'setting_type' => 'html',
                'value' => 'Starter App',
                'weight' => 2,
            ],
            [
                'name' => 'email_body',
                'description' => 'Notification email body.',
                'setting_type' => 'text',
                'value' => '',
                'weight' => 3,
            ],
        ],
    ];

    public static $setting_types = ['integer','ip','ip4','ip6','email','date','string','html','text'];

    public function run()
    {

        foreach(self::$setting_types as $id => $setting_type) {

            \DB::table('setting_types')->insert(['id' => $id+1, 'name' => $setting_type]);

        }

        foreach(self::$settings as $group_name => $setting_group) {

            $new_group = SettingGroup::all()->firstWhere('name', '=', $group_name);

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
