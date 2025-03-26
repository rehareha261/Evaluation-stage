<?php

namespace App\Api\v1\Services\Dashboard;

use App\Models\InvoiceLine;
use App\Models\Payment;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
class DashboardService{

    public function getSommeFactureMensuelle($year=null){
        $year = $year ?? now()->year;
        return InvoiceLine::whereRaw('YEAR(created_at)=?', [$year])
                            ->whereNotNull('invoice_id')
                            ->selectRaw('MONTH(created_at) as mois, SUM(quantity * price) as somme_mensuelle')
                            ->groupByRaw('MONTH(created_at)')
                            ->pluck('somme_mensuelle', 'mois')
                            ->toArray();
        ;
    }

    public function getSommePaymentMensuelle($year=null){
        $year = $year ?? now()->year;
        return Payment::whereRaw('YEAR(created_at)=?', [$year])
                            ->selectRaw('MONTH(payment_date) as mois, SUM(amount) as somme_mensuelle')
                            ->groupByRaw('MONTH(payment_date)')
                            ->pluck('somme_mensuelle', 'mois')
                            ->toArray();
        ;
    }

    public function getDataMensuelle($year=null){
        $year = $year ?? now()->year;
        $sommeFactureMensuelle = $this->getSommeFactureMensuelle($year);
        $sommePaymentMensuelle = $this->getSommePaymentMensuelle($year);

        $defaultValues = array_fill(1, 12, 0);
        $sommeFactureMensuelle = array_replace($defaultValues, $sommeFactureMensuelle);
        $sommePaymentMensuelle = array_replace($defaultValues, $sommePaymentMensuelle);

        $months = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];

        $mensuelles = [];
        for($i = 1 ; $i <= 12 ; $i++){
            $mensuelles[] = [
                $months[$i - 1] => [
                    "facture" => $sommeFactureMensuelle[$i],
                    "payee" => $sommePaymentMensuelle[$i]
                ]
            ];
        }
        return $mensuelles;
    }

    public function getPaymentRepartition(){
        return Payment::where('amount', '>=', 0)
                        ->selectRaw('SUM(amount) as somme, payment_source')
                        ->groupBy('payment_source')
                        ->get();
    }

    public function getEvolutionCA(){
        return Payment::where('amount', '>=', 0)
                            ->selectRaw('YEAR(payment_date) as annee, SUM(amount) as ca_annuelle')
                            ->groupByRaw('YEAR(payment_date)')
                            ->pluck('ca_annuelle', 'annee')
                            ->toArray();
    }

}
?>