<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    $offer = Offer::doesntHave('invoice')->inRandomOrder()->first();

    if (!$offer) {
        return [];
    }

    $lead = Lead::find($offer->source_id);

    $createdAt = $faker->dateTimeBetween($offer->created_at, '2025-12-31');

    return [
        'external_id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'status' => 'unpaid',
        'sent_at' => null,
        'due_at' => null,
        'client_id' => $offer->client_id,
        'integration_invoice_id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'integration_type' => 'external_system',
        'source_id' => $lead->id,
        'source_type' => Lead::class,
        'offer_id' => $offer->id,
        'remise' => 0.00,
        'created_at' => $createdAt
    ];
});

$factory->afterCreating(Invoice::class, function ($invoice, $faker) {
    $offer = Offer::find($invoice->offer_id);

    if ($offer) {
        $offer->setAsWon();
        foreach ($offer->invoiceLines as $line) {
            factory(InvoiceLine::class)->create([
                'invoice_id' => $invoice->id,
                'title' => $line->title,
                'external_id' => $line->external_id,
                'type' => $line->type,
                'quantity' => $line->quantity,
                'price' => $line->price,
            ]);
        }
    }
});

