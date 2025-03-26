<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'external_id' => $faker->uuid,
        'vat' => $faker->randomNumber(8),
        'company_name' => $faker->company(),
        'address' => $faker->secondaryAddress(),
        'city' => $faker->city(),
        'zipcode' => $faker->postcode(),
        'industry_id' => \App\Models\Industry::inRandomOrder()->first()->id ?? null,
        'user_id' => 1,
        'company_type' => 'ApS',
    ];
});
$factory->afterCreating(Client::class, function ($client) {
    factory(\App\Models\Contact::class)->create(
        [
            'client_id' => $client->id
        ]
    );
});
