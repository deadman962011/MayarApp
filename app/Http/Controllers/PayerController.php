<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\MayarOrder;
use App\MayarPayment;

class PayerController extends Controller
{
    //

    public function PayerLoginGet()
    {
        //
        return view('Payers.Login');
    }

    public function PayerLoginPost(Request $request)
    {
        if(!empty($request->session()->get('authenticatedPayer'))){
            return redirect()->route('PayerDashboardGet');
           
        }
        //validate inputs
        $validate=$request->validate([
            'PayerUserI'=>"required",
            'PayerPassI'=>"required"
        ]);

        //Check Payer Creds
        if ($validate['PayerUserI'] === config('getEnv.PayerUser') && $validate['PayerPassI'] === config('getEnv.PayerPass')) {
            $request->session()->put('authenticatedPayer', time());
            return redirect()->route("PayerDashboardGet");
        }
        else{
            return redirect()->route('PayerLoginGet')->with('err',['err'=>'0','message'=>'Username Or Password Is Wrong']);
        }

        
    }



    public function PayerDashboardGet()
    {
        # code...

        return  view('Payers.Dashboard');
        
    }

    public function OrderOneAj(Request $request)
    {

        //validate inputs 
        $validate = Validator::make(request()->all(), [
            
            'WayI'=>'required'
        ]);
        
        if ($validate->fails()) {
            return response(403);
        };

        if($request->input('WayI') == "Id" && !empty($request->input('IdI'))){
 
                //Check If Order Have Payment 
                $CheckOrder=MayarOrder::find($request->input('IdI'));

                if(!empty($CheckOrder) && $CheckOrder['OrderPayStatus'] == 1){

                    return response(405);

                }
                elseif(!empty($CheckOrder) && $CheckOrder['OrderPayStatus'] == 0){
                    
                    $CheckOrder->load('files');
                    $CheckOrder->load('customer');
                    $CheckOrder->load('service'); 
                    
                    //unSeriaLize Bill
                    return $CheckOrder;
                }
                else{
                    return response(403);
                }

        }
        elseif($request->input('WayI') == "Qr" && !empty($request->input('QrCodeI'))){
              
            //get Order By Qr Token
            $getOrder=MayarOrder::where('OrderQrToken',$request->input('QrCodeI'))->first();
            
            if(!empty($getOrder)){

                $getOrder->load('files');
                $getOrder->load('customer');
                $getOrder->load('service'); 
                
                //unSeriaLize Bill
                return $getOrder;
                
            }
            else{
                return response(403);
            }

        }
        
    }



    public function PayOrder(Request $request)
    {

        //validate Inputs 
        $validate = $request->validate([
            'OrderIdI'=>'required'
        ]);

        //Change Order pay Status to 1 => Paid

        $getOrder=MayarOrder::find($validate['OrderIdI']);

        if(!empty($getOrder)){

            //Check If Order Have Payment 
            $getPayment=MayarPayment::where('OrderId',$validate['OrderIdI'])->first();

            if(!empty($getPayment)){
                
                return  redirect()->route('PayerDashboardGet')->with('err',['err'=>'0','message'=>'Order Paid']);
            }


            $getOrder->update([
                'OrderPayStatus'=>1
            ]);

                //get Order Full Price

            //Unserialize Upgrades 
            $Upgrades=unserialize($getOrder['OrderUpgrades']);

            if(!empty($Upgrades)){

                $pricesAr=array();

                foreach ($Upgrades as $upgrade ) {
                
                    //Push prices And Sum IT
                    array_push($upgrade['UpgradePrice']);
                }

                $UpPrices=array_sum($pricesAr);
                $FullPrice=$UpPrices+$getOrder['OrderPrice'];
            }

            else{

                $FullPrice=$getOrder['OrderPrice'];
            }

            //Save Payment 
  				
            $SavePayment=new MayarPayment([
                'OrderId'=>$getOrder['id'],
                'PaymentMethod'=>'Local',
                'PaymentValue'=>$FullPrice,
            ]);

            $SavePayment->save();

            //Redirect To Print Bill

            return redirect()->route('PrintBill', ['OrderId' =>$getOrder['id']]);

        }

    }

    public function PrintBill(Request $request,$OrderId)
    {
        //get Order 
        $getOrder=MayarOrder::find($OrderId);
   
        
        if(empty($getOrder)){
            return redirect('/');
        }
        $getOrder->load('Service');

        //Get Bill
        $billArr=array();

        $ServiceName =$getOrder['Service']['ServiceName'];
        $ServicePrice=$getOrder['Service']['ServicePrice'];
        $Service=['Name'=>$ServiceName,'type'=>'Service','Price'=>$ServicePrice];
        array_push($billArr,$Service);


        //get Order upgrades And Full Price
        $Upgrades=unserialize($getOrder['OrderUpgradesId']);
        if(!empty($Upgrades)){

            $pricesAr=array();
            foreach ($Upgrades as $Upgrade ) {
                
                $UpgradeItem=['Name'=>$Upgrade['UpgradeTitle'],'type'=>'Service Upgrade','Price'=>$Upgrade['UpgradePrice']];
                array_push($billArr,$UpgradeItem);
                array_push($pricesAr,$Upgrade['UpgradePrice']);
            }

            $UpPrices=array_sum($pricesAr);
            $FullPrice=$UpPrices+$getOrder['OrderPrice'];
            
        }
        else{
            $FullPrice=$getOrder['OrderPrice'];
        }
        
         return view('includes.PrintBill',['FullPrice'=>$FullPrice ,'Bill'=>$billArr]);

    }



}
