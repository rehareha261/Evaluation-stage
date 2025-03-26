<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Appointment;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    $task = Task::inRandomOrder()->first();

    return [
        'external_id' => $faker->uuid,
        'title' => $faker->word,
        'description' => $faker->text,
        'start_at' => now(),
        'end_at' => now()->addHour(),
        'user_id' => 1,
        'source_type' => Task::class,
        'source_id' => $task->id,
    ];
});
