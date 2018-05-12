<?php

use App\Models\SettingGroup;
use Faker\Generator as Faker;

$factory->define(App\Models\Setting::class, function (Faker $faker) {

    $types = SettingsTableSeeder::$setting_types;
    $type = $faker->randomElement($types);
    $value = null;

    switch($type) {
        case 'integer':
            $value = $faker->randomNumber($faker->randomNumber(1));
            break;
        case 'ip':
        case 'ip4':
            $value = $faker->ipv4();
            break;
        case 'ip6':
            $value = $faker->ipv6();
            break;
        case 'email':
            $value = $faker->email();
            break;
        case 'date':
            $value = $faker->dateTimeBetween();
            break;
        case 'html':
            $value = $faker->randomHtml();
            break;
        case 'text':
        case 'string':
            $value = $faker->text();
            break;
    }

    $type = array_search($type, $types) + 1;

    return [
        'name' => substr($faker->unique()->word, 0, 12),
        'description' => $faker->sentence(12),
        'setting_type' => $type,
        'value' => $value,
        'weight' => $faker->randomNumber(1),
        'setting_group_id' => SettingGroup::all()->random()->first()->id
    ];
});
