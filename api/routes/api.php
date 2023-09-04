<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:api')->group(function() {
    Route::namespace('Api')->group(function() {


      Route::apiResource('users','UsersController');


      Route::apiResource('payments','PaymentsController ');
      Route::get('/tokenhelloasso', 'PaymentsController@getTokenHelloAsso');
      Route::post('/return', 'PaymentsController@return');
       
  
      Route::apiResource('appointements','AppointementsController');
      Route::get('/appointements/appointements_user/{id_user}', 'AppointementsController@appointementsByUser');
      Route::get('/appointements/appointements_usershort/{id_user}', 'AppointementsController@appointementsByUserShort');
      Route::get('/appointementByUser/{id_user}', 'AppointementsController@appointementByUser');
        

      Route::post('/change_password/{id}', 'AuthController@change_password');
      Route::get('/posts/posts_user/{id_user}', 'PostsController@postsByUser');
      Route::get('/posts/posts_user/{id_user}', 'PostsController@postsByUser');
      Route::get('/posts/posts_user_short/{id_user}', 'PostsController@postsByUserShort');
      Route::get('/userByrole', 'UsersController@userByrole');

      Route::apiResource('pages','PagesController');
      /////Invoices
      Route::apiResource('invoices','InvoicesController');
      Route::get('/invoicesByUser/{id_user}', 'InvoicesController@invoicesByUser');
      Route::get('invoiceid/{id}', 'InvoicesController@invoiceById');
      Route::get('invoice/{id}', 'InvoicesController@invoiceview');
      Route::post('invoicesend/{id}', 'InvoicesController@invoicesend');
      Route::post('addItemInvoice', 'InvoicesController@addItemInvoice');
      Route::post('updateAllprice', 'InvoicesController@updateAllprice');
      Route::post('updateInvoiceId/{id}', 'InvoicesController@updateInvoiceId');



  
  
     });
});

Route::namespace('Api')->group(function() {


        Route::apiResource('products','ProductsController');

        Route::post('/login', 'AuthController@login');
        Route::post('/register','AuthController@register');
        Route::post('/verifywithcode', 'VerificationApiController@verifywithcode');
        // Route::post('logout', 'AuthController@logout');
        Route::post('adduser', 'AuthController@adduser');
        Route::post('/forgotpassword', 'AuthController@resetpassword');
        Route::apiResource('tags', 'TagsController');
        Route::get('email/verify/{id}','VerificationApiController@verify')->name('verificationapi.verify');
        Route::get('email/resend','VerificationApiController@resend')->name('verificationapi.resend');
        Route::get('/verify/{token}', 'VerificationApiController@VerifyEmail');
        Route::post('testemail', 'EmailsController@testemail');
       /////Quotes

  
        Route::get('/clear-cache', function() {
          $exitCode = Artisan::call('cache:clear');
          // return what you want
        });

        });


          