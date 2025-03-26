<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Lead;
use App\Models\Client;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Lead::class, function (Faker $faker) {
    $client = Client::inRandomOrder()->first();
    $createdAt = $faker->dateTimeBetween('2023-01-01', '2025-12-31');
    $updatedAt = $faker->dateTimeBetween($createdAt, '2025-12-31');
    $deadline = $faker->dateTimeBetween($createdAt, '2025-12-31');


    return [
        'title' => $faker->sentence,
        'external_id' => $faker->uuid,
        'description' => $faker->paragraph,
        'user_created_id' => 1,
        'user_assigned_id' => 1,
        'client_id' => $client->id,
        'status_id' => $faker->numberBetween(2, 7),
        'deadline' => $deadline,
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ];
});

$factory->afterCreating(Lead::class, function ($lead, $faker) {
    $offersCount = $faker->numberBetween(0, 5);

    for ($i = 0; $i < $offersCount; $i++) {
        factory(\App\Models\Offer::class)->create([
            'client_id' => $lead->client_id,
            'source_id' => $lead->id,
        ]);
    }
});
