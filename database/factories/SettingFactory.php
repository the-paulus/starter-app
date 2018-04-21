<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Setting::class, function (Faker $faker) {

    $type = $faker->randomElement(['integer','ip','ip4','ip6','email','date','string']);
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
        case 'string':
            $value = $faker->text();
            break;
    }

    return [
        'name' => substr($faker->unique()->word, 0, 12),
        'description' => $faker->sentence(12),
        'type' => $type,
        'value' => $value,
        'weight' => $faker->randomNumber(1),
    ];
});
