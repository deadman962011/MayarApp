<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\carbon;
use Illuminate\Routing\Controller as BaseController;
use App\MayarNotif;
use App\MayarMessage;
use App\MayarOrder;
use App\MayarPayment;
use App\MayarCustomer;
use App;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function MainGet($lang)
    {
        if( $lang =='ar' or $lang =='en' or $lang =='tr')
        {
            App::setLocale($lang);
            session()->put('locale',$lang);
            return 'hello ';
        }
        else
        {
          return redirect()->route('main',["lang"=>'en']);
        }
    }
 
    public function ChangeNotif(Request $request)
    {

        //validate Input
        $validate = Validator::make(request()->all(), [
            'Type'=>'required',
        ]);

    
        if ($validate->fails()) {
            return response()->json(['err',['err'=>'0','message'=>$validate->messages()]]); 
        }



        //Check Type Of The Target Who Open The DropDown
        if($request->input('Type') === "Provider"){

            //Get Target Data
            $ServiceProviderTarget=Auth::guard('ServiceProvider')->user();
            $targetId=$ServiceProviderTarget['id'];

            
            //get Notifications Where Status Is 0 Not Fetched Yet
            $getNotif=MayarNotif::where([['NotifTargetType',1],['NotifTargetId',$targetId],['NotifStatus',0]])->get();

        }

        if($request->input('Type') === "BigBoss"){

            //get Target Data
            $BigBossTarget=$request->session()->get('Authenticated');
            
            //get Notifications Where Status Is 0 Not Fetched Yet
            $getNotif=MayarNotif::where([['NotifTargetType',0],['NotifTargetId','BigBoss'],['NotifStatus',0]])->get();

        }

        //Update Notification Status to 1 => Displayed On View
        foreach ($getNotif as $Notif ) {

            $NotifOne=mayarNotif::find($Notif['id']);
            //Update Notif
            $NotifOne->Update([
                'NotifStatus'=>1
            ]);
        }

        return response()->json(200);

    }

    // public function getMessages(Request $request)
    // {
    //     //get Messages
    //     $getMessages=MayarMessage::orderBy('created_at','Desc');

    //     return response()->json($getMessages, 200);
    // }

    public function CheckNM(Request $request)
    {

        if($request->input('AuthType') == 'Provider'){

            $targetType='1';
            $getProvider=Auth::guard('ServiceProvider')->user();
            $targetId=$getProvider['id'];

        }
        elseif($request->input('AuthType') =='BigBoss'){

            $targetType=0;
            $targetId=0;

        }

     
        //Check IF have New Message
   

        $getMessage=MayarMessage::where([['MessageStatus','0'],['MessageTargetType',$targetType],['MessageTarget',$targetId]])->count();


        //Check If have NEw Notifs
        
        $getNotif=MayarNotif::where([['NotifStatus','0'],['NotifTargetType',$targetType],['NotifTargetId',$targetId]])->count();

        return response()->json(['msg'=>$getMessage,'notif'=>$getNotif], 200);


    }


    public function  ChartsGet()
    {
        //MONTHLY Chart Start

            //get Orders and group them by month
            $monthArr=array();
            $dateOrders=MayarOrder::orderBy('created_at','ASC')->pluck('created_at');
            $dateOrders=json_decode($dateOrders);
            if(!empty($dateOrders)){
            
                foreach( $dateOrders as $UnfDate){
                    $date=new \DateTime($UnfDate);
                    $monthNa=$date->format( "M" );
                    $monthNo=$date->format( "m" );
                    $monthArr[ $monthNo ] = $monthNa; 
                }
            }

            //get Orders Count By Months
            if(!empty($monthArr)){
                $OrdersCountArr=array();
                $monthNameArr=array();
                foreach ($monthArr as $monthNo => $monthNa) {
                    $OrdersCount=MayarOrder::WhereMonth("created_at",$monthNo)->count();
                    array_push( $OrdersCountArr,$OrdersCount );
                    array_push($monthNameArr,$monthNa);
                }
            }
            //max num Of Orders
            if(!empty($OrdersCountArr)){
                $maxOrders=max( $OrdersCountArr);
                $maxOrders=round(($maxOrders + 10/2) /10)*10;
            }
             //month Chart array
            if(!empty($monthNameArr) && !empty($OrdersCountArr) && !empty($maxOrders)){
                $monthChartAr=array(
                    "months"=>array_slice($monthNameArr,-12),
                    "Orders"=>array_slice($OrdersCountArr,-12),
                    "MaxOrders"=>$maxOrders
                );
            }
                else{
                    $monthChartAr=array(
                        "months"=>["Today"],
                        "Orders"=>[0],
                        "MaxOrders"=>10
                    );
                }        
        //MONTHLY Chart End

        //PAE Chart Start
        
            //get Orders And Count Them them By Payment Way
            $getOredersPaeLocal=MayarPayment::where("PaymentMethod","Local")->count();
            $getOredersPaeCC=MayarPayment::where("PaymentMethod","CreditCard")->count();
            $getOredersPaePayPal=MayarPayment::where("PaymentMethod","PayPal")->count();

            //Get Count Of Orders Where Not Paid Yet
            $getOrderPaeWait=MayarOrder::where('OrderPayStatus','0')->count();

            $monthChartPae=array(
                "PaymentWay"=>[$getOredersPaeLocal,$getOredersPaeCC,$getOredersPaePayPal,$getOrderPaeWait]
            );

        //PAY Chart End


        //DAILY Chart Start 
        $DayArr=array();
        $DayOrder=MayarOrder::orderBy('created_at','ASC')->pluck('created_at');
        $DayOrder=json_decode($DayOrder);
        if(!empty($DayOrder)){
        
            foreach( $DayOrder as $UnfDateD){
                $date=new \DateTime($UnfDateD);
                $DayNa=$date->format( "D" );
                $DayNo=$date->format( "d" );
                $DayArr[ $DayNo ] = $DayNa; 
            }
        }


        //get Orders Count By Months
        if(!empty($DayArr)){
            $OrdersCountArrD=array();
            $DayNameArrD=array();
            foreach ($DayArr as $DayNo => $DayNa) {
                $OrdersCountD=MayarOrder::WhereMonth("created_at",carbon::now()->month)->WhereDay("created_at",$DayNo)->count();
                array_push( $OrdersCountArrD,$OrdersCountD );
                array_push($DayNameArrD,$DayNa);
            }
        }


        //max num Of Orders
        if(!empty($OrdersCountArrD)){
            $maxOrdersD=max( $OrdersCountArrD);
            $maxOrdersD=round(($maxOrdersD + 10/2) /10)*10;
        }

        //month Chart array
        if(!empty($DayNameArrD) && !empty($OrdersCountArrD) && !empty($maxOrdersD)){
            $DayChartArr=array(
                "Days"=>array_slice($DayNameArrD,-7),
                "Orders"=>array_slice($OrdersCountArrD,-7),
                "MaxOrders"=>$maxOrdersD
            );
        }
            else{
                $DayChartArr=array(
                    "Days"=>["Today"],
                    "Orders"=>[0],
                    "MaxOrders"=>10
                );
            } 

        //DAILY Chart End



        //Customers Daily Chart Start
        $CustDayArr=array();
        $CustDay=MayarCustomer::orderBy('created_at','ASC')->pluck('created_at');
        $CustDay=json_decode($CustDay);
        if(!empty($CustDay)){
        
            foreach( $CustDay as $UnfDateDCust){
                $date=new \DateTime($UnfDateDCust);
                $DayNa=$date->format( "D" );
                $DayNo=$date->format( "d" );
                $CustDayArr[ $DayNo ] = $DayNa; 
            }
        }


        //get Orders Count By Months
        if(!empty($CustDayArr)){
            $CustCountArrD=array();
            $CustDayNameArrD=array();
            foreach ($CustDayArr as $DayNo => $DayNa) {
                $CustCountD=MayarCustomer::WhereMonth("created_at",carbon::now()->month)->WhereDay("created_at",$DayNo)->count();
                array_push( $CustCountArrD,$CustCountD );
                array_push($CustDayNameArrD,$DayNa);
            }
        }


        //max num Of Orders
        if(!empty($CustCountArrD)){
            $maxCustsD=max( $CustCountArrD);
            $maxCustsD=round(($maxCustsD + 10/2) /10)*10;
        }

        //month Chart array
        if(!empty($CustDayNameArrD) && !empty($CustCountArrD) && !empty($maxCustsD)){
            $CustDayChartArr=array(
                "Days"=>array_slice($CustDayNameArrD,-7),
                "Custs"=>array_slice($CustCountArrD,-7),
                "MaxCusts"=>$maxCustsD
            );
        }
            else{
                $CustDayChartArr=array(
                    "Days"=>["Today"],
                    "Custs"=>[0],
                    "MaxCusts"=>10
                );
            } 

        //Customers Daily Chart End


        return response()->json(["AreaChart"=>$monthChartAr,"DayChart"=>$DayChartArr,"PaeChart"=>$monthChartPae,'CustChart'=>$CustDayChartArr], 200);


    }

}
