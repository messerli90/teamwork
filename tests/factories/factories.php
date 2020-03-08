<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Messerli90\Teamwork\Tests\Models\User;
use Messerli90\Teamwork\Tests\Models\Team;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'name' => $faker->firstName
    ];
});

$factory->define(Team::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'owner_id' => factory(User::class)
    ];
});
