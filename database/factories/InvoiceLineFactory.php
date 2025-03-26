<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InvoiceLine;
use Faker\Generator as Faker;

$factory->define(InvoiceLine::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'external_id' => $faker->uuid,
        'type' => $faker->randomElement(['pieces', 'hours', 'days', 'session', 'kg', 'package']),
        'quantity' => $faker->numberBetween(1, 9),
        'price' => $faker->numberBetween(10, 1000),
    ];
});


//
//$factory->afterCreating(Invoice::class, function ($invoice, $faker) {
//    $lineCount = $faker->numberBetween(1, 5);
//    factory(InvoiceLine::class, $lineCount)->create([
//        'invoice_id' => $invoice->id,
//    ]);
//
//    $totalAmount = $invoice->getTotalPriceAttribute()->getAmount();
//
//    if ($totalAmount > 0) {
//        $paymentOptions = [0, 0, 1, 2, 3, 0, 0, 0, 0, 0];
//
//        $numPayments = $paymentOptions[array_rand($paymentOptions)];
//
//        if ($numPayments > 0) {
//            $remainingAmount = $totalAmount;
//
//            for ($i = 0; $i < $numPayments; $i++) {
//                $paymentAmount = $faker->randomFloat(2, 10, min($remainingAmount, $totalAmount / 2));
//
//                $paymentDate = $faker->dateTimeBetween($invoice->sent_at, 'now + 2 months');
//
//                factory(\App\Models\Payment::class)->create([
//                    'invoice_id' => $invoice->id,
//                    'amount' => $paymentAmount,
//                    'payment_date' => $paymentDate,
//                ]);
//
//                $remainingAmount -= $paymentAmount;
//
//                if ($remainingAmount <= 0) {
//                    break;
//                }
//            }
//        }
//    }
//});
