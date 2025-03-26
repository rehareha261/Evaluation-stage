<?php

namespace App\Api\v1\Services\Invoices;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
class InvoiceService{
    public function getCountInvoiceSent(){
        return Invoice::all()->count();
    }
    public function getTotalPriceInvoices(){
        return InvoiceLine::whereNotNull("invoice_id")->sum(DB::raw("price * quantity"));
    }
    public function getInvoicesByStatus($status){
        $result = Invoice::with('client')->where('status', '=', $status)->get();
        return $result;
    }
    public function getInvoicesWithTotal(){
        $invoices = Invoice::join('invoice_lines', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->selectRaw('invoices.id, SUM(invoice_lines.quantity * invoice_lines.price) as total')
                ->whereNull('invoice_lines.deleted_at')
                ->groupBy('invoices.id')
                ->get();
        return $invoices->map(function ($invoice){
            return ['invoice' => Invoice::find($invoice->id),'total' => $invoice->total];
        });
    }
    public function setRemise($remise){
        if($remise > 100 || $remise < 0){
            throw new Exception("Le taux de remise ne peut pas etre superieur a 100 ou negatif");
        }
        $setting = Setting::first();
        $setting->remise = $remise;
        $setting->save();
    }
    public function getInvoices(){
        $draft = $this->getInvoicesByStatus(InvoiceStatus::draft());
        $closed = $this->getInvoicesByStatus(InvoiceStatus::closed());
        $unpaid = $this->getInvoicesByStatus(InvoiceStatus::unpaid());
        $sent = $this->getInvoicesByStatus(InvoiceStatus::sent());
        $partialPaid = $this->getInvoicesByStatus(InvoiceStatus::partialPaid());
        $paid = $this->getInvoicesByStatus(InvoiceStatus::paid());
        $overpaid = $this->getInvoicesByStatus(InvoiceStatus::overpaid());
        return ["draft" => $draft, "closed" => $closed, "sent" => $sent, "unpaid" => $unpaid, "partialPaid" => $partialPaid, "paid" => $paid, "overPaid" => $overpaid];
    }
}
?>