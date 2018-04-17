<?php

use Faker\Generator as Faker;

$factory->define(App\Models\PermissionGroup::class, function (Faker $faker) {
    return [
        'name' => substr($faker->unique()->word, 0, 12),
        'description' => $faker->sentence(12),
    ];
});
