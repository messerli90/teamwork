<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Messerli90\Teamwork\Tests\Models\User;
use Messerli90\Teamwork\Tests\Models\Team;
use Faker\Generator as Faker;
use Messerli90\Teamwork\TeamInvite;

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

$factory->define(TeamInvite::class, function (Faker $faker) {
    $types = ['invite', 'request'];

    return [
        'user_id' => factory(User::class),
        'team_id' => factory(Team::class),
        'type' => $types[array_rand($types)],
        'email' => $faker->email,
        'message' => $faker->paragraph,
        'accept_token' => md5(uniqid(microtime())),
        'deny_token' => md5(uniqid(microtime()))
    ];
});
