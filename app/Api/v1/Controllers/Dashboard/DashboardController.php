<?php

namespace App\Api\v1\Controllers\Dashboard;

use App\Api\v1\Controllers\ApiController;
use App\Api\v1\Services\Dashboard\DashboardService;
use App\Api\v1\Services\Invoices\InvoiceService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends ApiController{
    private $dashboardService;

    public function __construct(DashboardService $service){
        $this->dashboardService = $service;
    }

    public function getDataMensuelle(Request $request){
        $year = $request->input("year") ?? now()->year;

        $data = $this->dashboardService->getDataMensuelle($year);

        return $this->respondCreated(["mensuelle" => $data]);
    }

    public function getPaymentRepartition(){
        $repartition = $this->dashboardService->getPaymentRepartition();
        return $this->respondCreated(["repartition" => $repartition]);
    }


    public function getEvolutionCA(){
        $evolution = $this->dashboardService->getEvolutionCA();
        return $this->respondCreated(["evolution" => $evolution]);
    }

}



?>