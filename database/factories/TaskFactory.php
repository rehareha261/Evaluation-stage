<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $client = Client::inRandomOrder()->first();

    return [
        'title' => $faker->sentence,
        'external_id' => $faker->uuid,
        'description' => $faker->paragraph,
        'user_created_id' => 1,
        'user_assigned_id' => 1,
        'client_id' => $client->id,
        'status_id' => $faker->numberBetween($min = 1, $max = 4),
        'deadline' => $faker->dateTimeThisYear($max = 'now'),
        'created_at' => $faker->dateTimeThisYear($max = 'now'),
        'updated_at' => $faker->dateTimeThisYear($max = 'now'),
    ];
});
