<?php

use Faker\Generator as Faker;

$factory->define(App\Models\SettingGroup::class, function (Faker $faker) {
    return [
        'name' => substr($faker->unique()->word, 0, 12),
        'description' => $faker->sentence(12),
    ];
});
