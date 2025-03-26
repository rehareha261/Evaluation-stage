<?php
namespace App\Api\v1\Services\Offers;
use App\Models\Offer;
class OffersService{
    public function getCountOffers(){
        return Offer::all()->count();
    }
    public function getOffersByStatus($status){
        $result = Offer::with('client')->where('status', '=', $status)->get();
        return $result;
    }
    public function getOffers(){
        $won = Offer::with('client')->where('status', '=', 'won')->get();
        $lost = Offer::with('client')->where('status', '=', 'lost')->get();
        $inProgress = Offer::with('client')->where('status', '=', 'in-progress')->get();
        return ["won" => $won, "lost" => $lost, "inProgress" => $inProgress];
    }
}
?>