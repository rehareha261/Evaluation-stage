<?php

    namespace App\Api\v1\Services\Payments;

use App\Models\Payment;
use App\Services\Invoice\GenerateInvoiceStatus;
use App\Services\Invoice\InvoiceCalculator;
use Exception;
use Illuminate\Support\Facades\DB;

    class PaymentService{

        public function getTotalPricePayments(){
            return Payment::whereNull("deleted_at")->sum("amount");
        }

        public function getPayments(){
            return  Payment::whereNull("deleted_at")
                    ->where('amount', '>=', 0)
                    ->get();
        }

        public function updatePayment($externalId, $amount){
            DB::beginTransaction();
            try{
                $payment = Payment::where('external_id', $externalId)->first();
                $payment->amount = 0;    
                $payment->save();
                $invoice = $payment->invoice()->first();
                $invoiceCalculator = new InvoiceCalculator($invoice);
                $amountDue = $invoiceCalculator->getAmountDue()->getAmount();
                if($amountDue < $amount){
                    throw new Exception("Le nouveau montant ".$amount." depasse le montant restant ".$amountDue." total price ".$invoiceCalculator->getTotalPrice()->getAmount()." sum paiments ".$invoiceCalculator->getInvoice()->payments()->sum('amount'));
                }
                $payment->amount = $amount;
                $payment->save();
                $status = new GenerateInvoiceStatus($invoice);
                $status->createStatus();
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
                throw $e;
            }
        }

        public function softDeletePayments($externalId){
            $payment = Payment::where('external_id', $externalId)->first();

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $payment->delete();
            $payment->save();

            $invoice = $payment->invoice()->first();
            $status = new GenerateInvoiceStatus($invoice);
            $status->createStatus();
            return response()->json(['message' => 'Payment deleted successfully']);
        }



    }


?>