<?php

use App\Models\SettingGroup;
use Faker\Generator as Faker;

$factory->define(App\Models\Setting::class, function (Faker $faker) {

    $type = \DB::table('setting_types')->inRandomOrder()->get()->first();
    $value = null;

    switch($type->name) {
        case 'integer':
            $value = $faker->randomNumber($faker->randomNumber(1));
            break;
        case 'ip':
            $value = $faker->ipv4();
            break;
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
            $value = $faker->sentences(3, true);
            break;
        case 'string':
            $value = $faker->text();
            break;
    }

    return [
        'name' => substr($faker->unique()->word, 0, 12),
        'description' => $faker->sentence(12),
        'setting_type' => $type->name,
        'value' => $value,
        'weight' => $faker->randomNumber(1),
        'setting_group_id' => SettingGroup::all()->random()->first()->id
    ];
});
