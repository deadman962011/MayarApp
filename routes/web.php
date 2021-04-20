<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

use App\Mail\CustActivateMail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',function(){

   return redirect()->route('main',["lang"=>'en']);

  //return view('PayTabs');


});

Route::get('/{lang}',['uses'=>"Controller@MainGet"])->name('main');



// Route::post('/',function(Request $request){
//     if ($request->input('User') === env('BIGBOSSU') && $request->input('Pass') === env('BIGBOSSP')) {
//         $request->session()->put('authenticated', time());
//         return redirect('/test');
//     }

//     return "Somthing Worng";
// });

Route::post('ChangeNotif',['uses'=>'Controller@ChangeNotif','as'=>'ChangeNotifPost']);

Route::post('getMessages',['uses'=>"Controller@getMessages",'as'=>'getMessages']);

Route::post('CheckNM',['uses'=>'Controller@CheckNM','as'=>'CheckNM']);

Route::post('ChartsGet',['uses'=>'Controller@ChartsGet','as'=>'ChartsGet']);

Route::group(['prefix'=>'BigBoss'], function () {

    Route::get('/Login',['uses'=>'BigBossController@BigBossLoginGet','as'=>'BigBossLoginGet']);

    Route::post('/Login',['uses'=>"BigBossController@BigBossLoginPost",'as'=>"BigBossLogInPost"]);

    Route::group(['middleware' => ['web', 'BigBossAuth']], function () {

      Route::get('Dashboard',['uses'=>"BigBossController@DashboardGet",'as'=>"DashboardGet"]);

      Route::get('ProviderList',['uses'=>'BigBossController@ProviderListGet','as'=>'ProviderList']);

      Route::post('ProviderOne',['uses'=>'BigBossController@ProviderOne','as'=>'ProviderOne']);

      Route::post('SaveProvider',['uses'=>"BigBossController@SaveProvider",'as'=>'SaveProvider']);

      Route::post('UpdateProvider',['uses'=>'BigBossController@UpdateProvider','as'=>'UpdateProvider']);

      Route::post('DelProvider',['uses'=>'BigBossController@DelProvider','as'=>"DelProvider"]);

      Route::get('CategoryList',['uses'=>'BigBossController@CategoryListGet','as'=>'CategoryList']);

      Route::post('CategoryOne',['uses'=>"BigBossController@CategoryOne",'as'=>'CategoryOne']);

      Route::post('SaveCategory',['uses'=>"BigBossController@SaveCategory",'as'=>'SaveCategory']);

      Route::post('UpdateCategory',['uses'=>"BigBossController@UpdateCategory",'as'=>"UpdateCategory"]);

      Route::post('DelCategory',['uses'=>"BigBossController@DelCategory",'as'=>'DelCategory']);

      Route::get('MonthlyReports',['uses'=>'BigBossController@MonthlyReportsGet','as'=>'MonthlyReportsGet']);

      Route::get('DailyReports',['uses'=>'BigBossController@DailyReportsGet','as'=>'DailyReportsGet']);

      Route::get('TeamWork',['uses'=>'BigBossController@TeamWorkGet','as'=>'TeamWorkGet']);

      Route::post('SaveEmployee',['uses'=>'BigBossController@SaveEmployee','as'=>'SaveEmployee']);

      Route::post('getEmpAj',['uses'=>'BigBossController@getEmpAj','as'=>'getEmpAj']);

      Route::post('UpdateEmp',['uses'=>'BigBossController@UpdateEmp','as'=>'UpdateEmp']);

      Route::post('DelEmp',['uses'=>'BigBossController@DelEmp','as'=>'DelEmp']);

      Route::get('ProjList',['uses'=>'BigBossController@ProjListGet','as'=>'ProjListGet']);

      Route::post('SaveProj',['uses'=>'BigBossController@SaveProj','as'=>'SaveProj']);

      Route::post('DelProj',['uses'=>'BigBossController@DelProj','as'=>'DelProj']);

      Route::post('getProjAj',['uses'=>'BigBossController@getProjAj','as'=>'getProjAj']);

      Route::post('UpdateProj',['uses'=>'BigBossController@UpdateProj','as'=>'UpdateProj']);

      Route::get('Message',['uses'=>'BigBossController@MessageGet','as'=>'MessageBGet']);

      Route::post('Message',['uses'=>'BigBossController@MessagePost','as'=>'MessageBPost']);

      Route::get('LogOut',['uses'=>"BigBossController@LogOut","as"=>"LogOut"]);
    });

});

Route::group(['prefix'=>'Provider'], function () {

    Route::get('/Login',['uses'=>'ProviderController@ProviderLoginGet','as'=>'ProviderLoginGet']);

    Route::post('/Login',['uses'=>'ProviderController@ProviderLoginPost',"as"=>"ProviderLoginPost"]);

    Route::group(['middleware'=>['web','auth:ServiceProvider']], function () {

   //   Route::get('Dashboard',['uses'=>'ProviderController@ProviderDashboard','as'=>'ProviderDashboard']);

      Route::post('getCatProAjax',['uses'=>'ProviderController@getCatProAjax','as'=>'getCatProAjax']);

      Route::get('ServiceList',['uses'=>'ProviderController@ServiceListGet','as'=>'ServiceListGet']);

      Route::post('SaveService',['uses'=>'ProviderController@SaveService','as'=>'SaveService']);

      Route::post('UpdateService',['uses'=>'ProviderController@UpdateService','as'=>'UpdateService']);

      Route::post('DelService',['uses'=>'ProviderController@DeleteService','as'=>'DeleteService']);

      Route::post('GetUpgrades',['uses'=>'ProviderController@GetUpgrades','as'=>'GetUpgrades']);

      Route::post('SaveUpgrade',['uses'=>'ProviderController@SaveUpgrade','as'=>'SaveUpgrade']);

      Route::post('ChangeStatusSer',['uses'=>'ProviderController@ChangeStatusSer','as'=>'ChangeStatusSer']);

      Route::post('DelUpgrade',['uses'=>'ProviderController@DelUpgrade','as'=>'DelUpgrade']);

      Route::get('OrdersList',['uses'=>'ProviderController@OrderListGet','as'=>'OrderListGet']);

      Route::post('OrderDeliver',['uses'=>'ProviderController@OrderDeliver','as'=>'OrderDeliver']);

      Route::post('OrderCancel',['uses'=>'ProviderController@OrderCancel','as'=>'OrderCancel']);

      Route::post('OrderUploadFile',['uses'=>'ProviderController@OrderUploadFile','as'=>'OrderUploadFile']);

      Route::post('OrderSendMessage',['uses'=>'ProviderController@OrderSendMessage','as'=>'OrderSendMessage']);

      Route::get('OrderNeedPay',['uses'=>'ProviderController@OrderNeedPay','as'=>'OrderNeedPay']);

      Route::get('OrderCanceld',['uses'=>'ProviderController@OrderCanceld','as'=>'OrderCanceld']);

      Route::get('Message',['uses'=>'ProviderController@MessageGet','as'=>'MessageGet']);

      Route::post('Message',['uses'=>'ProviderController@MessagePost','as'=>'MessagePost']);

      Route::get('LogOut',['uses'=>'ProviderController@ProviderLogOut','as'=>'ProviderLogOut']);
        
    });
  });

    Route::group(['prefix' => 'Payer'], function () {
      
      Route::get('Login',['uses'=>'PayerController@PayerLoginGet','as'=>'PayerLoginGet']);
 
      Route::post('Login',['uses'=>'PayerController@payerLoginPost','as'=>'PayerLoginPost']);

      Route::post('OrderOneAj',['uses'=>'PayerController@OrderOneAj','as'=>'OrderOneAj']);

      Route::Post('PayOrder',['uses'=>"PayerController@PayOrder",'as'=>'PayOrder']);

      Route::group(['middleware' => ['web', 'PayerAuth']], function () {

         Route::get('Dashboard',['uses'=>'PayerController@PayerDashboardGet','as'=>'PayerDashboardGet']);

         Route::get('PrintBill/{OrderId}',['uses'=>'PayerController@PrintBill','as'=>'PrintBill']);

      });
    });

