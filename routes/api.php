<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('update',function (){
  return "OK";
})->middleware('updatedb');

Route::post('login','Auth\AuthController@login')->middleware('cors');
Route::post('logout','Auth\AuthController@logout')->middleware('cors');
Route::get('refreshToken','Auth\AuthController@refresh')->middleware('cors','jwt.auth');

Route::get('get_sidebar_menu','Security@getMenuSidebar');

Route::resource('master/master_division','Master\MasterDivisionController');
Route::resource('master/master_items','Master\MasterItemsController');
Route::resource('master/master_letter_type','Master\MasterLetterTypeController');
Route::resource('master/master_company','Master\MasterCompanyController');
Route::resource('master/customer','Master\CustomerController');

Route::post('change_pwd','Setup\UsersController@changePassword');
Route::resource('setup/users','Setup\UsersController');
Route::resource('setup/role','Setup\RoleController');

Route::resource('module/spk_header','Module\SpkHeaderController');
Route::resource('module/spk_detail','Module\SpkDetailController');
Route::resource('module/spmk_header','Module\SpmkHeaderController');
Route::resource('module/spmk_detail','Module\SpmkDetailController');
Route::resource('module/vendor','Module\VendorController');
Route::resource('module/bast_header','Module\BastHeaderController');
Route::resource('module/bast_detail','Module\BastDetailController');
Route::resource('module/bastp','Module\BastpController');
Route::resource('module/bahp','Module\BahpController');
Route::resource('module/bapp','Module\BappController');
Route::resource('module/bast','Module\BastController');
Route::resource('module/sp','Module\SpController');
Route::resource('module/spb','Module\SpbController');

Route::prefix('combo')->group(function () {
  Route::get('master_division', 'Master\MasterDivisionController@getMasterDivision');
  Route::get('master_items', 'Master\MasterItemsController@getMasterItems');
  Route::get('master_company', 'Master\MasterCompanyController@getMasterCompany');
  Route::get('master_letter_type', 'Master\MasterLetterTypeController@getMasterLetterType');
  Route::get('customer', 'Master\CustomerController@getCustomer');
  Route::get('spk_header', 'Module\SpkHeaderController@getSpkHeader');
  Route::get('sp', 'Module\SpController@getSp');
  Route::get('bahp', 'Module\BahpController@getBahp');
  Route::get('bast_header', 'Module\BastHeaderController@getBastHeader');
	Route::get('role', 'Setup\RoleController@getRole');
});

Route::prefix('data')->group(function () {
	Route::post('dealers', 'Master\MasterDealersController@getData');
	Route::post('unit', 'Master\MasterUnitController@getData');
	Route::post('banner', 'Master\BannerController@getData');
	Route::post('news', 'Master\NewsController@getData');
	Route::post('dashboard', 'DashboardController@getData');
	Route::post('time_slot', 'Module\BookingServiceController@getSlotBooking');
  Route::post('get_menu', 'Setup\MenuController@getData');
});


Route::get('jam',function(){
  setlocale(LC_TIME, 'Indonesia');
  \Carbon\Carbon::setLocale('id');

  // $date = \Carbon\Carbon::parse('2018-12-01');
	$date = \Carbon\Carbon::today();
  $diff = $date->addDays(10)->format('Y-m-d');
    // $diff = $date->diffInDays('2015-11-29');
    // $diff = $date->format('l, d F Y');

	return $diff;
});

Route::get('word','Test@word');
Route::get('email','Master\MasterItemsController@testEmail');
