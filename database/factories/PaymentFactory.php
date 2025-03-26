<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Payment;
use App\Services\Invoice\GenerateInvoiceStatus;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    $invoice = \App\Models\Invoice::where('status', '!=', 'paid')
        ->has('invoiceLines')
        ->inRandomOrder()
        ->first();

    if (!$invoice) {
        return [];
    }

    $totalAmount = $invoice->invoiceLines->sum(function ($line) {
        return $line->quantity * $line->price;
    });

    $paidAmount = $invoice->payments()->sum('amount');
    $remainingAmount = $totalAmount - $paidAmount;

    if ($remainingAmount <= 0) {
        return [];
    }

    $paymentDate = $faker->dateTimeBetween($invoice->created_at, Carbon::parse($invoice->sent_at)->addMonths(8));

    return [
        'external_id' => $faker->uuid,
        'invoice_id' => $invoice->id,
        'amount' => $faker->randomFloat(2, 10, min($remainingAmount, $totalAmount / 2)),
        'payment_date' => $paymentDate,
        'payment_source' => array_rand(\App\Enums\PaymentSource::values()),
    ];
});
$factory->afterCreating(Payment::class, function ($payment, $faker) {
    $invoice = \App\Models\Invoice::find($payment->invoice_id);

    if(!$invoice->isSent()){
        $invoice->sent_at =  $invoice->created_at;
        $invoice->due_at = Carbon::parse($invoice->created_at)->addYears(1);
        $invoice->save();
    }

    if ($invoice) {
        $status = new GenerateInvoiceStatus($invoice);
        $status->createStatus();
    }
});