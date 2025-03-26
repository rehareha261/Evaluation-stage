<?php

namespace App\Api\v1\Controllers\Invoices;

use App\Api\v1\Controllers\ApiController;
use App\Api\v1\Services\Invoices\InvoiceService;
use Exception;
use Illuminate\Http\Request;

class InvoicesController extends ApiController{
    private $invoiceService;

    public function __construct(InvoiceService $invoice){
        $this->invoiceService = $invoice;
    }

    public function getCountInvoice(){
        return $this->respondCreated(["count" => $this->invoiceService->getCountInvoiceSent()]);
    }
    public function getTotalPriceInvoices(){
        return $this->respondCreated(["total" => $this->invoiceService->getTotalPriceInvoices()]);
    }
    public function getInvoices(){
        $data = $this->invoiceService->getInvoices();

        return $this->respondCreated($data);
    }
    public function getInvoicesWithTotal(){
        $data = $this->invoiceService->getInvoicesWithTotal();
        return $this->respondCreated(['invoices' => $data]);
    }
    public function setRemise(Request $request){
        $request->validate([
            'remise' => 'required|numeric|min:0',
        ]);
        $amount = $request->input('remise');
        try{
            $this->invoiceService->setRemise($amount);
            return $this->respondCreated(["message" => "Remise global applique sur les invoices"]);
        }catch(Exception $e){
            return $this->respondError($e->getMessage(), 400);
        }
    }


}



?>