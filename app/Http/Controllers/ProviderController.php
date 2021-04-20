<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Auth;

use App\MayarCategory;
use App\MayarService;
use App\MayarProvider;
use App\MayarOrder;
use App\ServiceUpgrades;
use App\MayarFile;
use App\MayarCustomer;
use App\MayarMessage;
use App\MayarNotif;
use App\MessageCh;
class ProviderController extends Controller
{
    //

    public function ProviderLoginGet()
    {
        //Protect Route
        if(!empty(Auth::guard('ServiceProvider')->user())){
            return redirect()->route('OrderListGet');
        }

        return view('Providers.LogIn');
    }

    public function ProviderLoginPost(Request $request)
    {
        //Protect Route
        if(!empty(Auth::guard('ServiceProvider')->user())){
            return redirect()->route('OrderListGet');
        }

        //validate Inputs
        $validate=$request->validate([
            'ProviderUserI'=>'required',
            'ProviderPassI'=>'required'
        ]);


        //Check Provider 
        if(Auth::guard('ServiceProvider')->attempt(
            array(
            'ProviderUserName'=>$validate['ProviderUserI'],
            'password'=>$validate['ProviderPassI']
            ))){
                return redirect()->route('OrderListGet');        
            }
            else{
                return redirect()->route('ProviderLoginGet')->with('err',['err'=>'0','Username Or Password Is Wrong']);
            }
    }

    public function ProviderLogOut()
    {
        Auth::guard('ServiceProvider')->logOut();

        return redirect('/');
    }

    // public function ProviderDashboard()
    // {
    //     return view('Providers.Dashboard');
    // }

    public function getCatProAjax(Request $request)
    {
        //get Categories
        $getCategory=MayarCategory::all();
        
        //get Providers
        $getProvider=MayarProvider::all();

        return response()->json(['Categories'=>$getCategory,'Providers'=>$getProvider], 200,);

        
    }

    public function ServiceListGet()
    {

        //get Provider 
        $getProvider=Auth::guard('ServiceProvider')->user();

        //get Services
        $getServices=MayarService::where('ServiceProviderId',$getProvider['id'])->get();
        $getServices->load('Category');
        $getServices->load('ServiceProvider');
        $getServices->load('Upgrades');
        $getServices->load('Comments.Customer');

        return View('Providers.ServiceList',['Services'=>$getServices]);

    }


    public function SaveService(Request $request)
    {
        set_time_limit(300);
        //validate Inputs
        $validate=$request->validate([
            'ServiceNameI'=>'required',
            'ServiceThumbnailI'=>'required|max:1999',
            'ServicePriceI'=>'required|numeric',
            'ServiceCatI'=>'required',
            'ServiceDescI'=>'required',
        ]);


        //get Provider Id From Session
        $ProviderId=Auth::guard('ServiceProvider')->user();

        //Upload Service  Thumbnail

        //get Order Folder 
        $dir=config('getEnv.SerThumbFolder');


        // Check File Input
        if($request->hasFile("ServiceThumbnailI")){

            //get Orgenal FIle Name 
            $OFilename=$request->file('ServiceThumbnailI')->getClientOriginalName();
        
        //Upload File To Cache Folder 
            $filename=Storage::cloud()->put($dir,$request->file("ServiceThumbnailI"),file_get_contents($request->file('ServiceThumbnailI')));
            
            //get Uploaded File BaseName 
            $recursive = false; // Get subdirectories also?
            $contents = collect(Storage::cloud()->listContents($dir, $recursive));
            $file = $contents
                ->where('type', '=', 'file')
                ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                ->first(); // there can be duplicate file names!
            
            //Set Permission
            $service = Storage::cloud()->getAdapter()->getService();
            $permissionService = new \Google_Service_Drive_Permission();
            $permissionService->role = "reader";
            $permissionService->type = "anyone"; // anyone with the link can view the file
            $service->permissions->create($file['basename'], $permissionService);

        }

        else{
            return redirect()->route('ServiceListGet')->with('err',['err'=>'0','message'=>'Cant Upload Thumbnail']);
        }
        //Save Service
        $SaveService=new MayarService([
            'ServiceName'=>$validate['ServiceNameI'],
            'ServiceThumb'=>$file['basename'],  // $file['basename']
            'ServicePrice'=>$validate['ServicePriceI'],
            'ServiceProviderId'=>$ProviderId['id'],
            'ServiceCatId'=>$validate['ServiceCatI'],
            'ServiceDesc'=>$validate['ServiceDescI'],
            'ServiceOrderdNum'=>0,
            'ServiceStatus'=>0
        ]);
        $SaveService->save();

        // find Category And Increase Service Num In Category +1
        $getCategory=MayarCategory::find($validate['ServiceCatI']);
        if(!empty($getCategory)){
            $Plus1=$getCategory['CategoryServiceNum']+1;
            $UpdateCategory=$getCategory->Update([
                'CategoryServiceNum'=>$Plus1
            ]);
        }

        // find Provider And Increase Service Num In Provider +1
        $getProvider=MayarProvider::find($ProviderId['id']);
        if(!empty($getProvider)){
            $PlusProv=$getProvider['ProviderServiceNum']+1;

            $UpdateProvider=$getProvider->update([
                'ProviderServiceNum'=>$PlusProv
            ]); 
        }

        return redirect()->route('OrderListGet')->with('err',['err'=>'1','message'=>'Service Succesfully Saved']);
    }


    public function DeleteService (Request $request)
    {
        
        //Validate inputs 
        $validate=$request->validate([
            'DelSerIdI'=>'required',
            'ProviderPassI'=>'required'
        ]);

        //Check Provider 
        $Provider=Auth::guard('ServiceProvider')->user();

        if(Auth::guard('ServiceProvider')->attempt(
            array(
            'ProviderUserName'=>$Provider['ProviderUserName'],
            'password'=>$validate['ProviderPassI']
            ))){
               
                //get Service
                $getService=MayarService::find($validate['DelSerIdI']);
                if(!empty($getService)){


                //Delete Servcice Upgrades
                $getUpgrades=ServiceUpgrades::where('ServiceId',$getService['id'])->get();
              
                foreach ($getUpgrades as $Upgrade) {

                    //Find Upgrade And Delete It
                    $getUpgrade=ServiceUpgrades::find($Upgrade['id']);
                    $getUpgrade->delete();
                    
                }

                //Decrease Category Service Num
                $getCat=MayarCategory::find($getService['ServiceCatId']);
                if(!empty($getCat)){
                    
                    //Decrese Service Num
                    $getCat->update([
                        'CategoryServiceNum'=>$getCat['CategoryServiceNum']-1,
                    ]);
                }


                //Remove Thumbnail 
                
                  $dir=config('getEnv.SerThumbFolder');
                  //get Uploaded File BaseName 
  
                //Delete Old File 
                  $recursive = false; // Get subdirectories also?
                  $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
                  $Rmfile = $Rmcontents
                      ->where('type', '=', 'file')
                      ->where('basename', '=',$getService['ServiceThumb'])
                      ->first();
                  if(!empty($Rmfile)){
  
                  //delete Old Thumbnail On Cloud
                  Storage::delete($getService['getService']);  

                  }


                //Delete Service
                $getService->delete();

                    return redirect()->route('OrderListGet')->with('err',['err'=>'1','message'=>'Service Succesfully Deleted']);

                }
            }
    }


    public function GetUpgrades(Request $request)
    {
        
        
        //validate input
        if(empty($request->input('ServiceIdI'))){
            return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
        }
        

        //Check Service And get upgrades
        $getService=MayarService::find($request->input('ServiceIdI'));

        if(!empty($getService)){
            //get Upgrades
            $getupgrades=ServiceUpgrades::where('ServiceId',$request->input('ServiceIdI'))->get();
            return response()->json(['err',['err'=>'1','Upgrades'=>$getupgrades]],200);
        }
        else{
            return response()->json(['err',['err'=>'0','message'=>'Serevice Not Found']], 400);
        }

    }


    public function SaveUpgrade(Request $request)
    {
        
        //validate Inputs
        $validate = Validator::make(request()->all(), [
            'SerUpTitleI'=>'required',
            'SerUpPriceI'=>'required|integer',
            'SerUpDescI'=>"required",
            'ServiceIdI'=>'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
    
        }

        //Check Service And Save Upgrade
        $getService=MayarService::find($request->input('ServiceIdI'));

        if(!empty($getService)){

            //Save Upgrade
  
            $SaveUpgrade= new ServiceUpgrades([
                'UpgradeTitle'=>$request->input('SerUpTitleI'),
                'UpgradeDesc'=>$request->input('SerUpDescI'),
                'UpgradePrice'=>$request->input('SerUpPriceI'),
                'ServiceId'=>$request->input('ServiceIdI')
            ]);

            $SaveUpgrade->save();

            return response()->json(['err',['err'=>'1','message'=>'Upgrade Saved']], 200);
        }
        else{
            return response()->json(['err',['err'=>'0','message'=>'Serevice Not Found']], 400);
        }
    }


    public function ChangeStatusSer(Request $request)
    {
        //validate inputs
        $validate = Validator::make(request()->all(), [
            'ServiecIdI'=>'required',
            'SerStausI'=>'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
    
        }



        //Check service 
        $getService=MayarService::find($request->input('ServiecIdI'));
        if(empty($getService)){

            return response(400);
        }

        //Update Service Status

        if($request->input('SerStausI') == 0){

            $status=1;
        }
        elseif($request->input('SerStausI') == 1){

            $status=0;
        }


        $getService->update([
            'ServiceStatus'=>$status
        ]);

        return response(200);


    }


    public function DelUpgrade(Request $request)
    {
        
        //vaildate input
        if(empty($request->input('UpgradeIdI'))){

          return response('ValidateionErr',400);

        }

        //Check Upgrade And Delete It
        $getUpgrade=ServiceUpgrades::find($request->input('UpgradeIdI'));

        if(empty($getUpgrade)){

            return response(400);
        }
        else{

            //Delete Upgrade
            $getUpgrade->delete();
            return response(200);
        }        
    }


    public function UpdateService(Request $request)
    {
        set_time_limit(300);
        //Validate inputs
        $validate =$request->validate([
            'ServiceNameUI'=>'required',
            'ServicePriceUI'=>"required|numeric",
            'ServiceCatUI'=>'required',
            'ServiceDescUI'=>'required',
            'ServiceIdUI'=>'required',
            'ServiceThumbnailUI'=>'max:1999'
        ]) ;


        
        //Check Service Update It
        $getService=MayarService::find($validate['ServiceIdUI']);

        if(empty($getService))
        {
            return redirect()->route('ServiceListGet')->with('err',['err'=>'0','Sonthing Wrong']);
        }
        else
        {
            //Check If Update Have New File 
            if($request->hasFile("ServiceThumbnailUI")){

              //Upload New File
                
                //get Orgenal FIle Name 
                $OFilename=$request->file('ServiceThumbnailUI')->getClientOriginalName();
            

                //Upload File To Service Thumb Folder 
                $dir=config('getEnv.SerThumbFolder');
                $filename=Storage::cloud()->put($dir,$request->file("ServiceThumbnailUI"),file_get_contents($request->file('ServiceThumbnailUI')));
                
                //get Uploaded File BaseName 
                $recursive = false; // Get subdirectories also?
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                    ->first(); // there can be duplicate file names!

                //Set Permission
                $service = Storage::cloud()->getAdapter()->getService();
                $permissionService = new \Google_Service_Drive_Permission();
                $permissionService->role = "reader";
                $permissionService->type = "anyone"; // anyone with the link can view the file
                $service->permissions->create($file['basename'], $permissionService);

              //Delete Old File 
                $recursive = false; // Get subdirectories also?
                $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
                $Rmfile = $Rmcontents
                    ->where('type', '=', 'file')
                    ->where('basename', '=',$getService['ServiceThumb']);

                //delete Old Thumbnail On Cloud
                Storage::delete($getService['ServiceThumb']);  

                
            }

            //get Old Category And Decrase Service Num On Old Category
            $getOldCat=MayarCategory::find($getService['ServiceCatId']);
            if(!empty($getOldCat)){
                
                //Decrese Service Num
                $getOldCat->update([
                    'CategoryServiceNum'=>$getOldCat['CategoryServiceNum']-1,
                ]);
            }


            //get Old Category And Increase Service Num On Old Category 
            $getNewCat=MayarCategory::find($validate['ServiceCatUI']);
            if(!empty($getNewCat)){


                //Decrese Service Num
                $getNewCat->update([
                    'CategoryServiceNum'=>$getNewCat['CategoryServiceNum']+1,
                ]);
            }


           //Update Service
           $getService->update([
            'ServiceName'=>$validate['ServiceNameUI'],
            'ServiceThumb'=>$file['basename'],
            'ServicePrice'=>$validate['ServicePriceUI'],
            'ServiceCatId'=>$validate['ServiceCatUI'],
            'ServiceDesc'=>$validate['ServiceDescUI'],
           ]); 

           return redirect()->route('ServiceListGet')->with('err',['err'=>'1','message'=>'Service Successfully updated']);
        }
    }


    public function OrderListGet()
    {
        //get Provider 
        $getProvider=Auth::guard('ServiceProvider')->user();


        //get Orders 
        $getOrders=MayarOrder::where([['OrderTargetId',$getProvider['id']],['OrderStatus','=',"0"]])->get();
        $getOrders->load('Customer');
        $getOrders->load('Service');
        $getOrders->load('Files');

        //Transform Service Upgrades
        $transformUpgrades= $getOrders->transform(function($Upgrade){ 
            $Upgrade->OrderUpgradesId=unserialize($Upgrade->OrderUpgradesId);
            return $Upgrade;
          });

        return view('Providers.OrdersList',['Orders'=>$transformUpgrades]);

    }

    public function OrderCancel(Request $request)
    {
        //validate inputs
        $validate=$request->validate([
            'OrderIdI'=>'required'
        ]);

        //Check Order
        $getOrder=MayarORder::find($validate['OrderIdI']);

        if(!empty($getOrder)){

            //Change Order Status TO 2 ->>>> Cancel
            $getOrder->update([
                'OrderStatus'=>2
            ]);

        //Send  Notif To Customer When Cancel Order
        $saveNotif=new MayarNotif([
            'NotifTargetType'=>2,
            'NotifTargetId'=>$getOrder['OrderCustomerId'],
            'NotifValue'=>'OrderCanceld',
            'NotifStatus'=>0
        ]);
        $saveNotif->save();

            return redirect()->route('OrderListGet')->with('err',['err'=>'1','message'=>'Order Successfully updated']);

        }
        else{
            return redirect()->route('OrderListGet')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
        }

    }


    public function OrderDeliver(Request $request)
    {
       
        //validate Input
        $validate=$request->validate([
            'OrderIdI'=>'required'
        ]);
        
        //Check Order
        $getOrder=MayarOrder::find($validate['OrderIdI']);

        if(!empty($getOrder)){

            //Change Order Status TO 1 ->>>> Deliver
            $getOrder->update([
                'OrderStatus'=>1
            ]);

        //Send Notif To Customer When Deliver Order

        $saveNotif=new MayarNotif([
            'NotifTargetType'=>2,
            'NotifTargetId'=>$getOrder['OrderCustomerId'],
            'NotifValue'=>'OrderDeliverd',
            'NotifStatus'=>0
        ]);
        $saveNotif->save();

            return redirect()->route('OrderListGet')->with('err',['err'=>'1','message'=>'Order Successfully updated']);

        }
        else{
            return redirect()->route('OrderListGet')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
        }

    }


    public function OrderUploadFile(Request $request)
    {

        //validate input
        if(empty($request->input('OrderIdUplI'))){
            return  response()->json('ValidationErr', 400);
        }

        

        //Get And Check Order 
        $getOrder=MayarOrder::find($request->input('OrderIdUplI'));
        if(!empty($getOrder)){
            
            //get Order Folder 
            $dir=$getOrder['OrderFolder'];

            
            //Check File Input
            if($request->hasFile("file")){

                //get Orgenal FIle Name 
                $OFilename=$request->file('file')->getClientOriginalName();
            
            //Upload File To Cache Folder 
                $filename=Storage::cloud()->put($dir,$request->file("file"),file_get_contents($request->file('file')));
                
                //get Uploaded File BaseName 
                $recursive = false; // Get subdirectories also?
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                    ->first(); // there can be duplicate file names! 
        
                // Save File Info On DB
                $SaveFile=new MayarFile([
                    'BaseName'=>$file['basename'],
                    'FileName'=>pathinfo($OFilename, PATHINFO_FILENAME),
                    'StorageName'=>$file['filename'],
                    'Ext'=>$file['extension'],
                    'FileSource'=>1,
                    'OrderId'=>$getOrder['id']
                ]);
                $SaveFile->save();
                return  response(200);
            }
            else{
                return  response(400);
            }
        }
    }

    public function OrderSendMessage(Request $request)
    {
      
        //Validate inputs 
        $validate=$request->validate([
            'MessageTitleI'=>'required',
            'MessageTargetI'=>'required',
            'MessageSubjectI'=>'required',
            'MessageBodyI'=>'required',
            'MessageCustomerIdI'=>'required',
            'MessageOrderIdI'=>'required'
        ]);

        //Check Customer 
        $getCustomer=MayarCustomer::find($validate['MessageCustomerIdI']);

        if(empty($getCustomer)){
            return response(400);
        }

        //get Provider
        $Provider=Auth::guard('ServiceProvider')->user();

        //Set message target 
        if($validate['MessageTargetI'] == 'Customer'){
            $targetType=2; //Customer
        }
        elseif($validate['MessageTargetI'] == 'Admins'){
            $targetType=0; //BigBoss
        }

        //Set message Source
        $SourceType=1; //Provider
   
        //Save Message      
        $SaveMsg=new MayarMessage([
            'MessageTarget'=>$getCustomer['id'],
            'MessageSource'=>$Provider['id'],
            'MessageValue'=>$validate['MessageBodyI'],
            'MessageStatus'=>0,
            'MessageOrderId'=>$validate['MessageOrderIdI'],
            'MessageTargetType'=>$targetType,
            'MessageSourceType'=>$SourceType
        ]);
        $SaveMsg->save();

        return redirect()->route('OrderListGet')->with('err',['err'=>'1','message'=>'Message Successfully Sended']);

    }

    public function OrderNeedPay()
    {
        //
        //get Provider 
        $getProvider=Auth::guard('ServiceProvider')->user();


        //get Orders 
        $getOrders=MayarOrder::where([['OrderTargetId',$getProvider['id']],['OrderPayStatus','0'],['OrderStatus','1']])->get();
        $getOrders->load('Customer');
        $getOrders->load('Service');
        $getOrders->load('Files');

        //Transform Service Upgrades
        $transformUpgrades= $getOrders->transform(function($Upgrade){ 
            $Upgrade->OrderUpgradesId=unserialize($Upgrade->OrderUpgradesId);
            return $Upgrade;
        });

        //Get Orders Need Pay 

        return view('Providers.OrderNeedPayList',['Orders'=>$transformUpgrades]);
    }

    public function OrderCanceld()
    {
        //
                //
        //get Provider 
        $getProvider=Auth::guard('ServiceProvider')->user();


        //get Orders 
        $getOrders=MayarOrder::where([['OrderTargetId',$getProvider['id']],['OrderStatus','2']])->get();
        $getOrders->load('Customer');
        $getOrders->load('Service');
        $getOrders->load('Files');

        //Transform Service Upgrades
        $transformUpgrades= $getOrders->transform(function($Upgrade){ 
            $Upgrade->OrderUpgradesId=unserialize($Upgrade->OrderUpgradesId);
            return $Upgrade;
        });

        //Get Orders Need Pay 

        return view('Providers.OrderCanceldList',['Orders'=>$transformUpgrades]);

    }

    public function MessageGet()
    {
    
        //get Provider ID
        $ProviderId=Auth::guard('ServiceProvider')->user();
        
        //get Channels 
        $getCh=MessageCh::where('ProviderId',$ProviderId['id'])->get();
        if(!empty($getCh)){
            $getCh->load('Messages');
            $getCh->load('Customer');
        }


        return view('Providers.Messages',['Contacts'=>$getCh]);
        
    }

    public function MessagePost(Request $request)
    {
        //validate Inputs 
        $validate=$request->validate([
            'ContactIdI'=>'required',
            'MsgValueI'=>'required'
        ]);

        //get Channel Info 
        $getCh=MessageCh::find($validate['ContactIdI']);

        if(!empty($getCh)){

         
            //Save Message
            $SaveMsg=new MayarMessage([
                'MessageTarget'=>$getCh['CustomerId'],
                'MessageSource'=>$getCh['ProviderId'],
                'MessageValue'=>$validate['MsgValueI'],
                'MessageStatus'=>0,
                //'MessageOrderId'=>$validate['MessageOrderIdI'],
                'MessageTargetType'=>2,
                'MessageSourceType'=>1,
                'MessageCh'=>$getCh['id']
            ]);

            $SaveMsg->save();

            return redirect()->route('ServiceListGet')->with('err',['err'=>'0','message'=>'Message Succesfully Sended']);

        }

        

        
    }





}
