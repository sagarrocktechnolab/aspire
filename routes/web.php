<?php
	/*
	Project Name: RockTechnolabs
	Project URI: http://RockTechnolabs.com
	Author: RockTechnolabs Team
	Author URI: http://RockTechnolabs.com
	Version: 2.1
	*/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header('Access-Control-Allow-Origin:  *');
	header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
	header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| App Controller Routes
|--------------------------------------------------------------------------
|
| This section contains all Routes of application
| 
|
*/

Route::group(['namespace' => 'App'], function () {
	
	
	//registration url
	Route::post('/registerDevices', 'CustomersController@registerDevices');

	//registration url
	Route::post('/processRegistration', 'CustomersController@processRegistration');

	//update customer info url
	Route::post('/updateCustomerInfo', 'CustomersController@updateCustomerInfo');

	//update customer password url
	//Route::post('/updateCustomerPassword', 'CustomersController@updateCustomerPassword');

	// login url
	Route::post('/processLogin', 'CustomersController@processLogin');

	//push notification setting
	Route::post('/notify_me', 'CustomersController@notify_me');

	// forgot password url
	Route::post('/processForgotPassword', 'CustomersController@processForgotPassword');

	/*
	*  Loan and Repaymernt of it
	*
	*/
	Route::post('/applyForLoan', 'CustomersloanController@applyForLoan');
	Route::post('/loanRepay', 'CustomersloanController@loanRepay');
	

	
});
