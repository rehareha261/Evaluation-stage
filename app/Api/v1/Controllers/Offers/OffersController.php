<?php

namespace App\Api\v1\Controllers\Offers;

use App\Api\v1\Controllers\ApiController;
use App\Api\v1\Services\Offers\OffersService;

class OffersController extends ApiController{
    private $offerService;
    public function __construct(OffersService $offers){
        $this->offerService = $offers;
    }
    public function getCountOffers(){
        return $this->respondCreated(['count' => $this->offerService->getCountOffers()]);
    }
    public function getOffers(){
        $data = $this->offerService->getOffers();
        return $this->respondCreated($data);
    }
}

?>