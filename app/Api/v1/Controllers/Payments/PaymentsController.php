<?php


namespace App\Api\v1\Controllers\Payments;

use App\Api\v1\Controllers\ApiController;
use App\Api\v1\Services\Payments\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentsController extends ApiController{
    private $paymentService;

    public function __construct(PaymentService $payment){
        $this->paymentService = $payment;
    }

    
    public function getTotalPricePayments(){
        return $this->respondCreated(["total" => $this->paymentService->getTotalPricePayments()]);
    }

    public function getPayments(){
        return $this->respondCreated(["payments" => $this->paymentService->getPayments()]);
    }

    public function cancelPayment($externalID, Request $request){
        $this->paymentService->softDeletePayments($externalID);
        return $this->respondCreated(["message" => "Payment canceled"]);
    }


    public function updatePayment($externalID, Request $request){
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);
        $amount = $request->input('amount');
        try{
            $this->paymentService->updatePayment($externalID, $amount);
            return $this->respondCreated(["message" => "Payment amount updated"]);
        }catch(Exception $e){
            return $this->respondError($e->getMessage(), 400);
        }
    }
    

}


?>