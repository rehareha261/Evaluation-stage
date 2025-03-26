<?php

namespace App\DTOs;

use App\Enums\InvoiceStatus;
use App\Enums\OfferStatus;
use App\Models\Client;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Product;
use App\Models\Task;
use App\Models\User;
use App\Services\Exception\ImportException;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class InvoiceDTO{

    public $client_name;

    public $lead_title;

    public $type;

    public $produit;
    
    public $prix;

    public $quantite;

    public $task;

    public $project;

    public function setClient_name($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le client n'a pas de nom");
        }
        $this->client_name = $value;
    }
    public function setLead_title($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le lead n'a pas de titre");
        }
        $this->lead_title = $value;
    }

    public function setType($value) {
        if (strcasecmp($value, "invoice") !== 0 && strcasecmp($value, "offers") !== 0) {
            throw new Exception("Le type " . $value . " est inconnu");
        }
        $this->type = strtolower($value);
    }
    

    public function setProduit($value){
        if(is_null($value) || $value === ''){
            throw new Exception("Le produit n'a pas de nom");
        }
        $this->produit = $value;
    }

    public function setPrix($value){
        if(is_numeric($value)){
            $floatVal = floatval($value);
            if($floatVal < 0){
                throw new Exception("Le prix ne peut pas etre negatif");
            }
            $this->prix = $floatVal;
        }else{
            throw new Exception("Le prix n'est pas un nombre");
        }
    }


    public function setQuantite($value){
        if(is_numeric($value)){
            $floatVal = floatval($value);
            if($floatVal < 0){
                throw new Exception("La quantite ne peut pas etre negatif");
            }
            $this->quantite = $floatVal;
        }else{
            throw new Exception("La quantite n'est pas un nombre");
        }
    }
    public function __construct() {}

    //verifier qualified
    //type produit
    //produit archived

    public function generateLead($adminId, $statusOpenId, $clientId){
        $startDate = time();
        $endDate = strtotime('2030-12-01');
        $randomTimestamp = rand($startDate, $endDate);
        $deadline = date('Y-m-d', $randomTimestamp);
        $lead = new Lead(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'title' => $this->lead_title,
                'description' => Str::random(30),
                'status_id' => $statusOpenId,
                'user_assigned_id' => $adminId,
                'client_id' => $clientId,
                'user_created_id' => $adminId,
                'qualified' => 0,
                'deadline' => $deadline
            ]
        );
        return $lead;
    }
    public function genarateOffer($clientId, $leadId){
        $sentAt = date('Y-m-d', time());
        $status = $this->type === 'invoice' ? OfferStatus::won()->getStatus() : OfferStatus::inProgress()->getStatus();
        $offer = new Offer(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'sent_at' => $sentAt,
                'source_type' => Lead::class,
                'source_id' => $leadId,
                'client_id' => $clientId,
                'status' => $status
            ]
        );
        return $offer;
    }

    public function generateProduit(){
        $product = new Product(
            [
                'name' => $this->produit,
                'external_id' => Uuid::uuid4()->toString(),
                'description' => Str::random(20),
                'number' => random_int(10, 500),
                'default_type' => 'hours',
                'archived' => 0,
                'price' => $this->prix
            ]
        );
        return $product;
    }

    public function genererInvoice($leadId, $clientId, $offerId){
        $sentAt = date('Y-m-d', time());
        $invoice = new Invoice(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'status' => InvoiceStatus::unpaid()->getStatus(),
                'invoice_number' => random_int(1000, 100000),
                'sent_at' => $sentAt,
                'source_type' => Lead::class,
                'source_id' => $leadId,
                'client_id' => $clientId,
                'offer_id' => $offerId,
                'remise' => 0
            ]
        );
        return $invoice;
    }

    public function genererInvoiceLine($productId, $invoiceId, $offerId, $type){
        $invoiceLine = new InvoiceLine(
            [
                'external_id' => Uuid::uuid4()->toString(),
                'title' => Str::random(10),
                'comment' => Str::random(40),
                'price' => $this->prix,
                'invoice_id' => $invoiceId,
                'offer_id' => $offerId,
                'type' => $type,
                'quantity' => $this->quantite,
                'product_id' => $productId
            ]
        );
        return $invoiceLine;
    }
    

    public function traiter($adminId, $statusOpenId, $ligne){
        $client = Client::where('company_name', '=', $this->client_name)->first();
        if(is_null($client)){
            throw new ImportException("", ["Ligne ".$ligne." : le client ".$this->client_name." n'existe pas"]);
        }
        $lead = Lead::where('title', '=', $this->lead_title)->first();
        if(is_null($lead)){
            $lead = $this->generateLead($adminId, $statusOpenId, $client->id);
            $lead->save();
        }

        $product = Product::where('name', '=', $this->produit)->first();
        if(is_null($product)){
            $product = $this->generateProduit();
            $product->save();
        }

        $offer = $this->genarateOffer($client->id, $lead->id);
        $offer->save();

        $invoiceLine = $this->genererInvoiceLine($product->id, null, $offer->id, $product->default_type);

        $invoiceLine->save();

        if($this->type === 'invoice'){
            $invoice = $this->genererInvoice($lead->id, $client->id, $offer->id);

            $invoice->save();

            $invoiceLine = $this->genererInvoiceLine($product->id, $invoice->id, null, $product->default_type);

            $invoiceLine->save();
        }
    }

}


?>