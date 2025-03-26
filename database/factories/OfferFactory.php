<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\OfferStatus;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offer;
use Faker\Generator as Faker;
use Ramsey\Uuid\Uuid;

$factory->define(Offer::class, function (Faker $faker) {
    return [
        'external_id' => Uuid::uuid4()->toString(),
        'status' => OfferStatus::inProgress()->getStatus(),
        'source_type' => Lead::class,
    ];
});
$factory->afterCreating(Offer::class, function ($offer, $faker) {
    $lineCount = $faker->numberBetween(1, 5);

    factory(InvoiceLine::class, $lineCount)->create([
        'offer_id' => $offer->id,
    ]);
});