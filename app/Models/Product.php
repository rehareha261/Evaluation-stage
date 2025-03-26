<?php

namespace App\Models;

use App\Constante\Constante;
use App\Repositories\Money\Money;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";
    protected $fillable = [
        'external_id',
        'name',
        'description',
        'number',
        'default_type',
        'archived',
        'integration_type',
        'integration_id',
        'price'
    ];
    protected $appends = ['divided_price'];
    protected $hidden=['id'];

    public function getRouteKeyName()
    {
        return 'external_id';
    }

    public function getMoneyPriceAttribute()
    {
        $money = new Money($this->price);
        return $money;
    }

    public function getDividedPriceAttribute()
    {
        return $this->price / Constante::COEFFICIENT;
    }

}
