<?php

use App\Http\Controllers\Auth\AuthApiController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthApiController::class, 'login']);

Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('users', ['uses' => 'UserController@index']);
    });
});


// Invoices routes
Route::group(['namespace' => 'App\Api\v1\Controllers\Invoices'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('invoices/count', ['uses' => 'InvoicesController@getCountInvoice']);
        Route::get('invoices/total-price', ['uses' => 'InvoicesController@getTotalPriceInvoices']);
        Route::get('invoices', ['uses' => 'InvoicesController@getInvoices']);
        Route::get('invoices/details', ['uses' => 'InvoicesController@getInvoicesWithTotal']);
        Route::put('invoices/remise', ['uses' => 'IfnvoicesController@setRemise']);
    });
});

// Offers routes
Route::group(['namespace' => 'App\Api\v1\Controllers\Offers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('offers/count', ['uses' => 'OffersController@getCountOffers']);
        Route::get('offers/details', ['uses' => 'OffersController@getOffers']);
    });
});

// Payments routes
Route::group(['namespace' => 'App\Api\v1\Controllers\Payments'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('payments/total-price', ['uses' => 'PaymentsController@getTotalPricePayments']);
        Route::get('payments', ['uses' => 'PaymentsController@getPayments']);
        Route::put('payments/{externalID}/update', ['uses' => 'PaymentsController@updatePayment']);
        Route::delete('payments/{externalID}/delete', ['uses' => 'PaymentsController@cancelPayment']);
    });
});


// Dashboard routes
Route::group(['namespace' => 'App\Api\v1\Controllers\Dashboard'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('dashboard/mensuelle', ['uses' => 'DashboardController@getDataMensuelle']);
        Route::get('dashboard/payment/repartition', ['uses' => 'DashboardController@getPaymentRepartition']);
        Route::get('dashboard/chiffre-affaire/evolution', ['uses' => 'DashboardController@getEvolutionCA']);
    });
});
