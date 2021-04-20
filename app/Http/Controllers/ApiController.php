<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use QrCode;
use Carbon\Carbon;


use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Mail\CustActivateMail;
use App\Mail\CustRestPassMail;
use App\MayarCustomer;
use App\MayarOrder;
use App\MayarService;
use App\ServiceUpgrades;
use App\MayarNotif;
use App\MayarMessage;
use App\MayarFile;
use App\MayarCategory;
use App\MayarRate;
use App\MessageCh;
use App\MayarProvider;
use App\MayarTeam;
use App\MayarProj;


class ApiController extends Controller
{
    //

    public function PresentData()
    {
        //get Team Work Limit 5
        $getEmp=MayarTeam::limit(5)->get();

        //get Site Rates Limit 5
        $getRate=MayarRate::limit(5)->get();

        //get Projects Limit 5 
        $getProj=MayarProj::limit(5)->get();

        //get Count Of Customers 
        $CustCount=MayarCustomer::count();

        //get Count Of Services
        $ServCount=MayarService::count();

        //get Count Of Orders
        $OrdersCount=MayarOrder::count();

        return response()->json(['code'=>200,'message'=>'PresentGetSuccesErr','status'=>true,'item'=>['Rates'=>$getRate,'Emps'=>$getEmp,'Projs'=>$getProj,'ServCount'=>$ServCount,'OrderCount'=>$OrdersCount,'CustCount'=>$CustCount]], 200);
        //Done
    }


    public function EmpAll($limit,$SortKey,$SortType)
    {
        //
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getEmp=MayarTeam::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getEmp=MayarTeam::limit($limit)->get();
        }

        //return response()->json($getService,200);
        return response()->json(['code'=>200,'message'=>'GetEmpSucsessErr','status'=>true,'item'=>$getEmp],200);
    }

    public function ProjAll($limit,$SortKey,$SortType)
    {
        //
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getProj=MayarProj::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getProj=MayarProj::limit($limit)->get();
        }

        //return response()->json($getService,200);
        return response()->json(['code'=>200,'message'=>'GetProjSucsessErr','status'=>true,'item'=>$getProj],200);

    }


    public function CustRegister(Request $request)
    {
        //validate Inputs
        $validate = Validator::make(request()->all(), [
            'CustFirstNameI'=>'required',
            'CustLastNameI'=>'required',
            'CustUserNameI'=>"required|min:8",
            'CustPassI'=>'required|min:8',
            'CustPass2I'=>'required|min:8',
            'CustMailI'=>'required|email',
            'CustCountryI'=>'required',
        ]);

        if ($validate->fails()) {
           // return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }
        
        //Check If Email Used Before
        $getCustMail=MayarCustomer::where('Custmail',$request->input('CustMailI'))->count();
        if($getCustMail >0){
           // return response()->json(['err',['err'=>'0','message'=>'MailUsedErr']],400);
           return response()->json(['code'=>400,'message'=>'MailUsedErr','status'=>false,'item'=>null],400);
        }


        //Check If UserName Used Befor
        $getCustUserName=MayarCustomer::where('CustUserName',$request->input('CustUserNameI'))->count();
        if($getCustUserName >0){
           // return response()->json(['err',['err'=>'0','message'=>'UserNameUsedErr']],400);
           return response()->json(['code'=>400,'message'=>'UserNameUsedErr','status'=>false,'item'=>null],400);
        }

        //Check If Passowrds Match
        if($request->input('CustPassI') != $request->input('CustPass2I')){
           // return response()->json(['err',['err'=>'0','message'=>'PasswordNotMatchErr']],400);
            return response()->json(['code'=>400,'message'=>'PasswordNotMatchErr','status'=>false,'item'=>null],400);
            
        }

        //generate Activation Token 
        $AcivationToken= md5(rand(1, 10) . microtime());
        $PassRestToken=md5(rand(1, 12) . microtime());

        //Save Customer
        $SaveCustomer=new MayarCustomer([
            'CustFirstName'=>$request->input('CustFirstNameI'),
            'CustLastName'=>$request->input('CustLastNameI'),
            'CustUserName'=>$request->input('CustUserNameI'),
            'CustPass'=>bcrypt($request->input('CustPassI')),
            'CustMail'=>$request->input('CustMailI'),
            'CustStatus'=>0, // Not Activated Need Activation
            'CustCountry'=>$request->input('CustCountryI'),
            'CustAddress'=>$request->input('CustAddressI'),
            'CustActivationToken'=>$AcivationToken,
            'CustPassRestToken'=>$PassRestToken,
            'CustPassRestExpire'=>1
        ]);

        $SaveCustomer->save();

        //Send Activation link to Customer mail
        $ActivationUrl=config('getEnv.ActivateUrl');
        Mail::to($SaveCustomer['CustMail'])->send(new CustActivateMail($AcivationToken,$ActivationUrl));


        //Send Success Response
        //return response()->json(['err',['err'=>'1','message'=>'CustRegisterSuccessErr']], 201);
        return response()->json(['code'=>201,'message'=>'CustRegisterSuccessErr','status'=>true,'item'=>$SaveCustomer],201);
    }



    public function CustLogIn(Request $request)
    {

        //validate Inputs
        $validate = Validator::make(request()->all(), [
            'CustUserNameI'=>"required",
            'CustPassI'=>'required'
        ]);
        if ($validate->fails()) {
           // return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }

        //Chcek UserName Input Value
        //LogIn With Email
         if(filter_var($request->input('CustUserNameI'), FILTER_VALIDATE_EMAIL)){
            if (!$token = Auth::guard('api')->attempt(
                array(
                'CustMail'=>$request->input('CustUserNameI'),
                'password'=>$request->input('CustPassI')
                ))) {
    
            //return response()->json(['err',['err'=>'1','message' => 'UnauthorizedErr']], 401);
            return response()->json(['code'=>401,'message'=>'UnauthorizedErr','status'=>false,'item'=>null],401);
            }    

            //get Customer
            $getCust=Auth::guard('api')->user();
            
           // return response()->json([ 'err'=>'0','Customer'=>$getCust,'token' => $token,'expires' => auth('api')->factory()->getTTL() * 60,]);
            return response()->json(['code'=>200,'message'=>'CustAuthSuccesErr','status'=>true,'item'=>$getCust,'token'=>$token],200);
         }
         //LogIn With UserName
         else{
            if (!$token = Auth::guard('api')->attempt(
                array(
                'CustUserName'=>$request->input('CustUserNameI'),
                'password'=>$request->input('CustPassI')
                ))) {


            //return response()->json(['err',['err'=>'1','message'=>'UnauthorizedErr']], 401);
              return response()->json(['code'=>401,'message'=>'UnauthorizedErr','status'=>false,'item'=>null],401);
    
            }    

            //get Customer
            $getCust=Auth::guard('api')->user();

           // return response()->json([ 'err'=>'0','Customer'=>$getCust,'token' => $token,'expires' => auth('api')->factory()->getTTL() * 60,]);
            return response()->json(['code'=>200,'message'=>'CustAuthSuccesErr','status'=>true,'item'=>$getCust,'token'=>$token],200);

         }
        

        //CheckCustomer

    }

    public function CustGetNotif()
    {
        //get Customer
        $getCust=Auth::guard('api')->user();

        //get Notifs
        $getNotifs=MayarNotif::where('NotifTargetId',$getCust['id'])->get();

        //return response()->json($getNotifs, 200);
        return response()->json(['code'=>200,'message'=>'GetNotifSucessErr','status'=>true,'item'=>$getNotifs],200);

        //Done
    }

    public function CustUpdateNotif()
    {
        //get Customer
        $getCust=Auth::guard('api')->user();

        //Update Notifs 
        $getNotifs=MayarNotif::where('NotifTargetId',$getCust['id'])->get();

        foreach ($getNotifs as $Notif ) {

            $NotifOne=MayarNotif::find($Notif['id']);
            //Update Notif
            $NotifOne->Update([
                'NotifStatus'=>1
            ]);
        }

        //return response()->json('Notifications Updated',201);
        return response()->json(['code'=>200,'message'=>'UpdateNotifSucessErr','status'=>true,'item'=>null],200);

        //Done
    }

    public function CustActiveResend()
    {
        //get Customer
        $getCust=Auth::guard('api')->user();

        //get Activation Token
        $AcivationToken=$getCust['CustActivationToken'];
        $CustMail=$getCust['CustMail'];

        //Send Activation link to Customer mail
        $ActivationUrl=config('getEnv.ActivateUrl');
        Mail::to($CustMail)->send(new CustActivateMail($AcivationToken,$ActivationUrl));

        return response()->json(['code'=>200,'message'=>'ResendMailSuccessErr','status'=>true,'item'=>null],200);
    }

    public function CustInfo()
    {
        $Customer=Auth::guard('api')->user();
        
        //return response()->json(['Customer'=>$Customer], 200,);
        return response()->json(['code'=>200,'message'=>'GetCustSucessErr','status'=>true,'item'=>$Customer],200);
    }

    public function CustEdit(Request $request)
    {
        //get Customer Id
        $Cust=Auth::guard('api')->user();
        $CustId=$Cust['id'];

        //get Customer  
        $getCust=MayarCustomer::find($CustId);

        //Check IF Pass Is Match
        if($request->input('CustPassI') != $request->input('CustPass2I')){
            //return response()->json(['err',['err'=>'0','message'=>'Passsword Not Match']], 200);
            return response()->json(['code'=>400,'message'=>'PasswordNotMatchErr','status'=>false,'item'=>null],400);
        }


        //Update Customer
        $UpdateCust=$getCust->update([
            'CustFirstName'=>$request->input('CustFirstNameI'),
            'CustLastName'=>$request->input('CustLastNameI'),
            'CustUserName'=>$request->input('CustUserNameI'),
            'CustPass'=>bcrypt($request->input('CustPassI')),
            'CustCountry'=>$request->input('CustCountryI'),
            'CustAddress'=>$request->input('CustAddressI'),
        ]);

        //return response()->json(['err',['err'=>'1','message'=>'CustUpdatedSuccesErr']], 200);
        return response()->json(['code'=>200,'message'=>'CustUpdatedSuccesErr','status'=>true,'item'=>null],200);
    }




    public function CustLogOut()
    {
        Auth::guard('api')->logout();

        //return response(200);
        return response()->json(['code'=>200,'message'=>'CustLogOutSuccesErr','status'=>true,'item'=>null],200);
        
    }


    public function SaveOrder(Request $request)
    {

        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'ServiceIdI'=>'required',
            'ServiceUpgradesI'=>'required',
            'CustomerIdI'=>"required"
        ]);
        

        if ($validate->fails()) {
            //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }

        //Check Customer
        $CheckCustomer=MayarCustomer::where([['id','=',$request->input('CustomerIdI')],['CustStatus','=','1']])->first();

        if(empty($CheckCustomer)){
         //   return response()->json(['err',['err'=>'0','message'=>'SWErr','error'=>'Cant Find Customer']], 500);
            return response()->json(['code'=>400,'message'=>'CantFindCustErr','status'=>false,'item'=>null],400);
        }

        //Check Service
        $CheckService=MayarService::where([['id','=',$request->input('ServiceIdI')],['ServiceStatus','=','1']])->first();        
        if(empty($CheckService)){
            //return response()->json(['err',['err'=>'0','message'=>'SWErr','error'=>'Cant Find Service']], 500);
            return response()->json(['code'=>400,'message'=>'CantFindServErr','status'=>false,'item'=>null],400);
        }
        $CheckService->load('ServiceProvider');
        // Check Service Upgrades
        $ServiceUpgradesArr=array();
        $ServiceUpgradesPriceArr=array();
        if($request->input('ServiceUpgradesI') !='null'){

            //Explode Value
            $UpgradesArrExpl=explode(',',$request->input('ServiceUpgradesI'));
    
          
            foreach ($UpgradesArrExpl as $ServiceUpgrade) {
                $getUpgrade=ServiceUpgrades::where([['id','=',$ServiceUpgrade],['ServiceId','=',$request->input('ServiceIdI')]])->get();
                array_push($ServiceUpgradesArr,$getUpgrade[0]);
                array_push($ServiceUpgradesPriceArr,$getUpgrade[0]['UpgradePrice']);
               
            }
        }else{
            $ServiceUpgradesArr=[];
        }

        //Serialize Upgrades
        $SerializedUp=serialize($ServiceUpgradesArr);

        //Set TotalPrice
        $UpgradesArr=array_sum($ServiceUpgradesPriceArr);
        $ServicePrice=$CheckService['ServicePrice'];
        $TotalPrice=$UpgradesArr+$ServicePrice;


        //generate Random Qr Code 
        $QrToken=md5(rand(1, 10) . microtime());
        $Qr= QrCode::generate($QrToken);
        $encodeQr= base64_encode($Qr);


        //Save Order
        $SaveOrder=new MayarOrder([
            'OrderServiceId'=>$request->input('ServiceIdI'),
            'OrderCustomerId'=>$request->input('CustomerIdI'),
            'OrderStatus'=>0, //Isnt Completed Status
            'OrderUpgradesId'=>$SerializedUp,
            'OrderPrice'=>$TotalPrice,
            'OrderQr'=>$encodeQr,
            'OrderQrToken'=>$QrToken,
            'OrderTargetId'=>$CheckService['ServiceProvider']['id'],
            'OrderDesc'=>$request->input('OrderDescI')
        ]);
       $SaveOrder->save();

        //Add Notification To Provider
        $saveNotif=new MayarNotif([
         'NotifTargetType'=>1,
         'NotifTargetId'=>$CheckService['ServiceProvider']['id'],
         'NotifValue'=>'OrderSaved ',
         'NotifStatus'=>0
        ]);
        $saveNotif->save();

        //Add Notification To Customer
        $saveNotif=new MayarNotif([
            'NotifTargetType'=>2,
            'NotifTargetId'=>$request->input('CustomerIdI'),
            'NotifValue'=>'OrderSaved',
            'NotifStatus'=>0
            ]);
        $saveNotif->save();

        //Increase Service Orderd Num +1 

          //get Old Service orderd Num
          $OrderdNum=$CheckService['ServiceOrderdNum']+1;

        $UpdateSer=$CheckService->update([
            'ServiceOrderdNum'=>$OrderdNum
        ]);


    //    Create New Folder For The Order
       Storage::cloud()->makeDirectory($SaveOrder['id']);

       //get Folder BaseName
       $dir = '/';
       $recursive = false; // Get subdirectories also?
       $contents = collect(Storage::cloud()->listContents($dir, $recursive));
   
       $OrderDir = $contents->where('type', '=', 'dir')
           ->where('filename', '=',$SaveOrder['id'])
           ->first(); // There could be duplicate directory names!
   
       // Update Folder Column
       $SaveOrder->update([
           'OrderFolder'=>$OrderDir['basename']
       ]);

       //Check If Have Files  
       if(!empty($request->input('FilesArr'))){


        $FileArr = explode(",", $request->input('FilesArr'));
        foreach ($FileArr as $File) {

            //Update OrderID On DB
            $getFile=MayarFile::find($File);
            $getFile->Update([
                'OrderId'=>$SaveOrder['id']
            ]);

            //Move Files To Folder
            $contents2 = collect(Storage::cloud()->listContents($dir, $recursive));
   
            $StorageFile = $contents2->where('type', '=', 'file')
                ->where('basename', '=',$getFile['BaseName'])
                ->first(); // There could be duplicate directory names!
        
            $FileMove=Storage::cloud('google')->move($StorageFile['basename'],$OrderDir['basename'].'/'.$StorageFile['name']);
            //$FileMove=Storage::cloud()->move($StorageFile,$OrderDir['filename'].'/'.$StorageFile);   
        }
       }

        //return response()->json(['err',['err'=>'0','message'=>'SaveOrderSuccesErr']], 201);
        return response()->json(['code'=>201,'message'=>'SaveOrderSuccesErr','status'=>true,'item'=>$SaveOrder],201);
    }







    public function UploadFile(Request $request)
    {
        set_time_limit(300);
        $dir=config('getEnv.CacheThumbFolder');

       //Check Input
       if($request->hasFile("FileI")){

        //get Orgenal FIle Name 
        $OFilename=$request->file('FileI')->getClientOriginalName();
       
       //020 File To Cache Folder 
        $filename=Storage::cloud()->put($dir,$request->file("FileI"),file_get_contents($request->file('FileI')));
    
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

      
        // Save File Info On DB
        $SaveFile=new MayarFile([
            'BaseName'=>$file['basename'],
            'FileName'=>pathinfo($OFilename, PATHINFO_FILENAME),
            'StorageName'=>$file['filename'],
            'Ext'=>$file['extension'],
            'FileSource'=>0,
        ]);
        $SaveFile->save();

        //return response()->json(['err',['err'=>'0','message'=>'FileUploadSucessErr','File'=>$SaveFile]], 201);
        return response()->json(['code'=>201,'message'=>'FileUploadSucessErr','status'=>true,'item'=>$SaveFile],201);

       }
       else{
        //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']], 400);
        return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
       }

    }


    public function SaveMessage(Request $request)
    {
        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'MessageTargetI'=>'required',
            'MessageTargetTypeI'=>'required',
            'MessageValueI'=>"required",
        ]);

        if ($validate->fails()) {
            //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
           
        }


        if($request->input('MessageTargetTypeI') == 0){

            $Provider=0;
            $TargetType=0;


        }
        elseif($request->input('MessageTargetTypeI') == 1){

            $Provider=$request->input('MessageTargetI');
            $TargetType=1;

            //Check Provider 
            $CheckProv=MayarProvider::find($Provider);
            if(empty($CheckProv)){
                return response()->json(['code'=>400,'message'=>'CantFindProviderErr','status'=>false,'item'=>null],400);
            }

        }

        //get Customer ID
        $Cust=Auth::guard('api')->user();
        $CustId=$Cust['id'];

        //Check if Have Channel Room
        $CheckCh=MessageCh::where([['ProviderId',$Provider],['CustomerId',$CustId]])->first();

        if(empty($CheckCh)){
      
        $SaveCh=new MessageCh([
            'ProviderId'=>$Provider,
            'CustomerId'=>$CustId
        ]);
        $SaveCh->save();

        $Ch=$SaveCh;

        }
        else{

        $Ch=$CheckCh;

        }

        //CheckOrder
        // $getOrder=MayarOrder::find($request->input('MessageOrderIdI'));
   
        // if(empty($getOrder)){
            
        //     //return response()->json(['err',['err'=>'1','message'=>'SWErr']], 500);
        //     return response()->json(['code'=>400,'message'=>'CantFindOrderErr','status'=>false,'item'=>null],400);
        // }

        //Save Message 
        $saveMessage=new MayarMessage([
            'MessageTarget'=>$Provider,
            'MessageTargetType'=>$TargetType,
            'MessageSource'=>$CustId,
            'MessageSourceType'=>2,
            'MessageValue'=>$request->input('MessageValueI'),
            'MessageStatus'=>0, //Not Fetched Yet
            'MessageCh'=>$Ch['id']
            //'MessageOrderId'=>$request->input('MessageOrderIdI')
        ]);
        $saveMessage->save();
        //return response()->json(['err',['err'=>'0','message'=>'MessageSendSuccsErr']], 201);
        return response()->json(['code'=>201,'message'=>'MessageSendSuccsErr','status'=>true,'item'=>$saveMessage],201);

    }

    public function GetMessage()
    {

        //get Customer 
        $getCust=Auth::guard('api')->user();


        // Find Channel Where Customer
        $getCh=MessageCh::where('CustomerId',$getCust['id'])->get();
        if(!empty($getCh)){
            $getCh->load('Messages');
            $getCh->load('Customer');
            $getCh->load('Provider');
        }
        
        return response()->json(['code'=>200,'message'=>'MessageGetSucessErr','status'=>true,'item'=>$getCh], 200);

    }


    public function UpdateCh(Request $request)
    {
        
        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'ChIdI'=>'required',
            'SourceTypeI'=>'required',
        ]);

        if ($validate->fails()) {
            //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
            
        }

        if($request->input('SourceTypeI') == 1){
          // if Provider Send Request Update All MEssages in Ch From Cust
   
          $getMessages=MayarMessage::where([['MessageSourceType',2],['MessageCh',$request->input('ChIdI')]])->get();

          if(!empty($getMessages)){

            foreach ($getMessages as $Msg ) {
                
                //get Msg And Update Message Status
                $getMsg=MayarMessage::find($Msg['id']);
                if(!empty($getMsg)){
                    $updateMsg=$getMsg->update([
                        'MessageStatus'=>1
                    ]);
                }
            
            }
            return response()->json(['code'=>200,'message'=>'MessageUpdatedSuccessErr','status'=>true,'item'=>null],200);
          }
          
        }
        elseif($request->input('SourceTypeI') == 2){
            $getMessages=MayarMessage::where([['MessageSourceType',1],['MessageCh',$request->input('ChIdI')]])->get();

            if(!empty($getMessages)){
  
              foreach ($getMessages as $Msg ) {
                  
                  //get Msg And Update Message Status
                  $getMsg=MayarMessage::find($Msg['id']);
                  if(!empty($getMsg)){
                      $updateMsg=$getMsg->update([
                          'MessageStatus'=>1
                      ]);
                  }
              
              }
              return response()->json(['code'=>200,'message'=>'MessageUpdatedSuccessErr','status'=>true,'item'=>null],200);
            }
        }
        else{
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }

        
    }

    public function CustActivate(Request $request)
    {
        
                //Validate Inputs
                $validate = Validator::make(request()->all(), [
                    'ActivateCodeI'=>'required',
                ]);
                
        
                if ($validate->fails()) {
                    //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
                    return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
                }

                //get Customer who have Token 
                $getCust=MayarCustomer::where('CustActivationToken','=',$request->input('ActivateCodeI'))->first();

                //Check Customer And Change Status To 1 => Activated
                if(!empty($getCust)){
                    $getCust->update(['CustStatus'=>1]);

                    //return response()->json(['err',['err'=>'1','message'=>'ActivatedCustSuccesErr']],200);
                    return response()->json(['code'=>200,'message'=>'ActivatedCustSuccesErr','status'=>true,'item'=>null],200);
                }
                else{
                    //return response()->json(['err',['err'=>'0','message'=>'SWErr']],500);
                    return response()->json(['code'=>400,'message'=>'CantFindCustErr','status'=>false,'item'=>null],400);
                }
    }

    public function CustPassRestReq(Request $request)
    {

        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'CustMailI'=>'required',
        ]);
        

        if ($validate->fails()) {
            //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }
        
        //get Customer By Mail 
        $getCustomer=MayarCustomer::where([['CustMail',$request->input('CustMailI')]])->first();

        $CustStatus=$getCustomer['CustStatus'];
        //return $CustStatus;
        if($CustStatus === "0"){

            //return response()->json(['err',['err'=>'1','message'=>'AccountNotActivatedErr']],400);
            return response()->json(['code'=>400,'message'=>'AccountNotActivatedErr','status'=>false,'item'=>null],400);
        }

        //Check Mail And Send Rest Mail Message To it
        if(!empty($getCustomer)){

            $RestUrl=config('getEnv.RestPassUrl');
            $RestToken=$getCustomer['CustPassRestToken'];
           // Mail::to($getCustomer['CustMail'])->send(new CustRestPassMail($RestToken,$RestUrl));

            //return response()->json(['err',['err'=>'1','message'=>'RestMsgSendSuccesErr']],200);
            return response()->json(['code'=>200,'message'=>'RestMsgSendSuccesErr','status'=>true,'item'=>null],200);
        }
        else{
            //return response()->json(['err',['err'=>'0','message'=>'SWErr']],500);
            return response()->json(['code'=>400,'message'=>'CantFindCustErr','status'=>false,'item'=>null],400);
        }
        
    }

    public function CustRestPassExec(Request $request)
    {
    
    //validate inputs 
    $validate = Validator::make(request()->all(), [
        'RestPassTokenI'=>'required',
        'RestPass1I'=>'required',
        'RestPass2I'=>'required'
    ]);
    
    if ($validate->fails()) {
        //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
        return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
    }
    
    //get Customer By Rest Password
    $getCustomer=MayarCustomer::where([['CustPassRestToken',$request->input('RestPassTokenI')],['CustStatus','1']])->first();

 

        //Check if Passowrds matche
        if($request->input('RestPass1I') === $request->input('RestPass2I')){

                //Check Customer And Update Password
                if(!empty($getCustomer)){

                    $getCustomer->update(['CustPass'=>bcrypt($request->input('RestPass1I'))]);
                    //return response()->json(['err',['err'=>'1','message'=>'UpdatPassSuccesErr']],200);
                    return response()->json(['code'=>'22','message'=>'UpdatPassSuccesErr','status'=>true,'item'=>null],200);

                }
                else{
                    //return response()->json(['err',['err'=>'0','message'=>'SWErr']],500);
                    return response()->json(['code'=>400,'message'=>'CantFindCustErr','status'=>false,'item'=>null],400);
                }
        
        }
        else{
            //return response()->json(['err',['err'=>'0','message'=>'RestPassNotMatchErr']],403);
            return response()->json(['code'=>400,'message'=>'PasswordNotMatchErr','status'=>false,'item'=>null],400);
        }
    }


    public function ServiceAll($limit,$SortKey,$SortType)
    {
        
        //
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getService=MayarService::where('ServiceStatus','1')->limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getService=MayarService::where('ServiceStatus','1')->limit($limit)->get();
        }

        $getService->load('Category');
        $getService->load('Upgrades');
        $getService->load('ServiceProvider');
        
        //return response()->json($getService,200);
        return response()->json(['code'=>200,'message'=>'GetServSucsessErr','status'=>true,'item'=>$getService],200);

    }

    public function ServiceOne($ServiceId)
    {
        
        //Check Param

        if(is_numeric($ServiceId)){
            

            //get Service
            $getService=MayarService::find($ServiceId);
            if(!empty($getService)){

                $getService->load('Category');
                $getService->load('ServiceProvider');
                $getService->load('Upgrades');
                $getService->load('Comments');
        
                //return response()->json($getService,200);
                return response()->json(['code'=>200,'message'=>'GetServSucsessErr','status'=>true,'item'=>$getService],200);

            }
            else{
                //return response()->json(['err',['err'=>'0','message'=>'SWErr']], 500);
                return response()->json(['code'=>400,'message'=>'cantFindSerErr','status'=>false,'item'=>null],400);
            }

        }
        else{
                //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
                return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }
    }

    public function ServiceByCat($CatId,$limit,$SortType,$SortKey)
    {
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        if(!empty($CatId)){

            //Check OrderBy Inputs
            if($SortKey !="null"){
                $getService=MayarService::where('ServiceCatId',$CatId)->limit($limit)->orderBy($SortKey, $SortType)->get();
            }
            else{
                $getService=MayarService::where('ServiceCatId',$CatId)->limit($limit)->get();
            }
            $getService->load('Category');
            $getService->load('ServiceProvider');
            $getService->load('Upgrades');
            //return response()->json($getService,200);
            return response()->json(['code'=>200,'message'=>'GetServSucsessErr','status'=>true,'item'=>$getService],200);

        }

    }
    public function CategoryAll($limit,$SortKey,$SortType)
    {

        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getCategory=MayarCategory::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getCategory=MayarCategory::limit($limit)->get();
        }

        //return response()->json($getCategory,200);
        return response()->json(['code'=>'25','message'=>'GetCatSucessErr','status'=>true,'item'=>$getCategory],200);
    }


    public function CategoryOne($CategoryId)
    {
        
        //Check Param
        if(is_numeric($CategoryId)){
    
            //get Service
            $getCategory=MayarCategory::find($CategoryId);
            if(!empty($getCategory)){
        
                //return response()->json($getCategory,200);
                return response()->json(['code'=>200,'message'=>'GetCatSucessErr','status'=>true,'item'=>$getCategory],200);

            }
            else{
                //return response()->json(['err',['err'=>'0','message'=>'SWErr']], 500);
                return response()->json(['code'=>'26','message'=>'CantFindCatErr','status'=>false,'item'=>null],400);
            }

        }
        else{
                //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
                return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }
        
    }


    public function OrderAll($limit,$SortKey,$SortType)
    {
       

        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //get Customer ID
        $getCustomer=Auth::guard('api')->user();


        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getOrder=MayarOrder::where('OrderCustomerId',$getCustomer['id'])->limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getOrder=MayarOrder::where('OrderCustomerId',$getCustomer['id'])->limit($limit)->get();
        }

        //trasform Service Upgrades To Array
        $transformUpgrades= $getOrder->transform(function($OrderUpgradesId){ 
            $OrderUpgradesId->OrderUpgradesId=unserialize($OrderUpgradesId->OrderUpgradesId);
            return $OrderUpgradesId;  
            });

        $getOrder->load('Service');
        $getOrder->load('Customer');
        //return response()->json($getOrder,200);
        return response()->json(['code'=>400,'message'=>'GetOrderSucessErr','status'=>true,'item'=>$getOrder],200);
    }

    public function OrderOne($OrderId)
    {
        //Check Param
        if(is_numeric($OrderId)){


            // //get Customer ID
            $getCustomer=Auth::guard('api')->user();

            //get Order
            $getOrder=MayarOrder::where([['id',$OrderId],['OrderCustomerId',$getCustomer['id']]])->first();
            if(!empty($getOrder)){

            //trasform Service Upgrades To Array
            $transformUpgrades=transform($getOrder,function($OrderUpgradesId){ 
                $OrderUpgradesId->OrderUpgradesId=unserialize($OrderUpgradesId->OrderUpgradesId);
                return $OrderUpgradesId;  
                });

            $getOrder->load('Service');
            $getOrder->load('Customer');
        
                //return response()->json($getOrder,200);
                return response()->json(['code'=>400,'message'=>'GetOrderSucessErr','status'=>true,'item'=>$getOrder],200);

            }
            else{
                // return response()->json(['err',['err'=>'0','message'=>'SWErr']], 500);
                return response()->json(['code'=>'17','message'=>'CantFindOrderErr','status'=>false,'item'=>null],400);
            }

        }
        else{
                //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
                return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
        }
    }



    public function PayOrderPP(Request $request)
    {
        //

        //validate Inputs
        
        
        //get Order
        $getOrder=MayarOrder::find($request->input('OrderIdI'));
       

        if(empty($getOrder)){
            
            return response()->json(403);
        }

        // get Full Price

            //Unserialize Upgrades 
            $Upgrades=unserialize($getOrder['OrderUpgrades']);

            if(!empty($Upgrades)){

                $pricesAr=array();

                foreach ($Upgrades as $upgrade ) {
                
                    //Push prices And Sum IT
                    array_push($pricesAr,$upgrade['UpgradePrice']);
                }

                $UpPrices=array_sum($pricesAr);
                $FullPrice=$UpPrices+$getOrder['OrderPrice'];
            }

            else{

                $FullPrice=$getOrder['OrderPrice'];
            }

         //Generate payment Token
         $clientId=config('getEnv.PaypalId');
         $clientSecret=config('getEnv.PaypalSeceret');


         $environment = new SandboxEnvironment($clientId, $clientSecret);
         $client = new PayPalHttpClient($environment);
         $PayReq = new OrdersCreateRequest();
         $PayReq->prefer('return=representation');
         $PayReq->body = [
                              "intent" => "CAPTURE",
                              "purchase_units" => [[
                                  "reference_id" => "test_ref_id1",
                                  "amount" => [
                                      "value" => "100.00",
                                      "currency_code" => "USD"
                                  ]
                              ]],
                              "application_context" => [
                                   "cancel_url" => "https://example.com/cancel",
                                   "return_url" => "https://example.com/return"
                              ] 
                          ];
         
         try {

             // Call API with your client and get a response for your call
             $PayResponse = $client->execute($PayReq);
             
             // If call returns body in response, you can get the deserialized version from the result attribute of the response
             $Blaxkres=$PayResponse;

             return response()->json($Blaxkres->result, 200);
             
            }catch (HttpException $ex) {
                echo $ex->statusCode;
                
            }

    }


    public function PayOrderPPExec(Request $request)
    {
        //

        $OrderId= $request->input('OrderPayId');
        $CustomerId=$request->input('CustomerId');

        if(empty($CustomerId)){
             return response()->json('Customer Input Required', 403);
        }

        //Check Customer 
        $getCust=MayarCustomer::find($CustomerId);

        if(empty($getCust)){
            return response()->json('Somthing Wrong', 403);
        }

        if(!empty($OrderId)){

            $clientId=config('getEnv.PaypalId');
            $clientSecret=config('getEnv.PaypalSeceret');
   
   
            $environment = new SandboxEnvironment($clientId, $clientSecret);
            $client = new PayPalHttpClient($environment);


            $CapRequest = new OrdersCaptureRequest($OrderId);
            $CapRequest->prefer('return=representation');
            try {
                // Call API with your client and get a response for your call
                $CapResponse = $client->execute($CapRequest);
                
                //Send  Notif To Customer When Pay With PayPal Order
                $saveNotif=new MayarNotif([
                    'NotifTargetType'=>2,
                    'NotifTargetId'=>$getCust['id'],
                    'NotifValue'=>'PaymentDone',
                    'NotifStatus'=>0
                ]);
                $saveNotif->save();


                //Send  Notif To BigBoss When Pay With PayPal Order
                $saveNotif=new MayarNotif([
                    'NotifTargetType'=>0,
                    'NotifTargetId'=>0,
                    'NotifValue'=>'PaymentDone',
                    'NotifStatus'=>0
                ]);
                $saveNotif->save();



                // If call returns body in response, you can get the deserialized version from the result attribute of the response
                print_r($CapResponse);
            }catch (HttpException $ex) {
                echo $ex->statusCode;
                print_r($ex->getMessage());
            }
        }
    }


    // public function PayMayarExec(Request $request)
    // {
    //     //Validate Inputs
    //     $validate = Validator::make(request()->all(), [
    //         'OrderIdI'=>'required',
    //     ]);

    //     if ($validate->fails()) {
    //         return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
    
    //     }

    //     //Check The Order  And If THe Order Have Payment 

    //     $getOrder=MayarOrder::find($request->input('OrderIdI'));
    //     if(empty($getOrder)){

    //         return response()->json(['err',['err'=>'0','message'=>'SWErr']], 500);

    //     }

    //     //get Order Ful Price 

    //         //Unserialize Upgrades 
    //         $Upgrades=unserialize($getOrder['OrderUpgrades']);

    //         if(!empty($Upgrades)){

    //             $pricesAr=array();

    //             foreach ($Upgrades as $upgrade ) {
                
    //                 //Push prices And Sum IT
    //                 array_push($upgrade['UpgradePrice']);
    //             }

    //             $UpPrices=array_sum($pricesAr);
    //             $FullPrice=$UpPrices+$getOrder['OrderPrice'];
    //         }

    //         else{

    //             $FullPrice=$getOrder['OrderPrice'];
    //         }

    //     //Save Payment  

    //     $SavePayment=new MayarPayment([
    //         'OrderId'=>$getOrder['id'],
    //         'PaymentMethod'=>'Local',
    //         'PaymentValue'=>$FullPrice,
  
    //     ]);

    //     //Upldate Order Pay Status
    //     $getOrder->update([
    //         'OrderPayStatus'=>1,
    //     ]);

    //     //Done

    //     return response()->json(['err',['err'=>'0','message'=>'PaymentDone']], 201);
    // }

    public function SaveRate(Request $request)
    {
        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'RateValueI'=>'required|numeric',
            'RateBodyI'=>'required'
        ]);

        if ($validate->fails()) {
            //return response()->json(['err',['err'=>'0','message'=>'ValidationErr']],400);
            return response()->json(['code'=>400,'message'=>'ValidationErr','status'=>false,'item'=>null],400);
 
        }
        
        //get Customer 
        $getCust=Auth::guard('api')->user();

        //Save Rate
        $SaveRate=new MayarRate([
            'MayarRateCustId'=>$getCust['id'],
            'MayarRateValue'=>$request->input('RateValueI'),
            'MayarRateBody'=>$request->input('RateBodyI')
        ]);

        $SaveRate->Save();

        //return response()->json(['err',['err'=>'1','message'=>'RateSavedSucsessErr']],201);
        return response()->json(['code'=>201,'message'=>'RateSavedSucsessErr','status'=>true,'item'=>$SaveRate],201);

      //   
    }

    public function RateAll($limit,$SortKey,$SortType)
    {
       //           
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getRate=MayarRate::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getRate=MayarRate::limit($limit)->get();
        }

        $getRate->load('Customer');
        
        //return response()->json($getRate,200);
        return response()->json(['code'=>200,'message'=>'GetRatesSuccessErr','status'=>true,'item'=>$getRate],200);

    }




}
