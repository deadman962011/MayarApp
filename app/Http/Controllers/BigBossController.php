<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\MayarProvider;
use App\MayarCategory;
use App\MayarReport;
use App\MayarTeam;
use App\MayarProj;
use App\MayarMessage;
use App\MessageCh;
class BigBossController extends Controller
{
    //

    public function BigBossLoginGet(Request $request)
    {
        if(!empty($request->session()->get('authenticated'))){
            return redirect()->route('DashboardGet');
        }
        return view('BigBoss.LogIn');

     
    }


    public function BigBossLoginPost (Request $request)
    {

        if(!empty($request->session()->get('authenticated'))){
            return redirect()->route('DashboardGet');
           
        }
        //validate inputs
        $validate=$request->validate([
            'BigUserI'=>"required",
            'BigPassI'=>"required"
        ]);

        //Check BigBoss Creds
        if ($validate['BigUserI'] === config('getEnv.BigBossUser') && $validate['BigPassI'] === config('getEnv.BigBossPass')) {
            $request->session()->put('authenticated', time());
            return redirect()->route("DashboardGet");
        }
        else{
            return redirect()->route('BigBossLoginGet')->with('err',['err'=>'0','message'=>'Worng Password Or UserName']);
        }

        
    }



    public function DashboardGet()
    {
        return view('BigBoss.Dashboard');
    }

    public function LogOut(Request $request){ 
         $request->session()->forget('authenticated');
          return redirect('/');
    }


    public function ProviderListGet()
    {
        //get Providers
        $getProviders=MayarProvider::all();
        return view('BigBoss.ProviderList',['Providers'=>$getProviders]);
    }

    public function SaveProvider(Request $request)
    {
       //Validate Inputs
        $validate=$request->validate([
            'ProviderNameI'=>'required',
            'ProviderUserNameI'=>'required',
            'ProviderPasswordI'=>'required',
            'ProviderIconSrcI'=>'required',
            'ProviderDescI'=>'required'
        ]);

        //Check If Provider UserName Is Available 
        $CheckProvier=MayarProvider::where('ProviderName',$validate['ProviderNameI'])->count();
        if($CheckProvier > 1){
            return  redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'Provider UserName Is Already In Use']);
        }

        //Save Provider
        $SaveProvider=new MayarProvider([
            "ProviderName"=>$validate['ProviderNameI'],
            "ProviderUserName"=>$validate['ProviderUserNameI'],
            "ProviderPass"=>bcrypt($validate['ProviderPasswordI']),
            "ProviderIconSrc"=>$validate['ProviderIconSrcI'],
            "ProviderDesc"=>$validate['ProviderDescI'],
            "ProviderServiceNum"=>0
        ]);
        $SaveProvider->Save();

        return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Provider Succesfully Saved']);
    }

    public function UpdateProvider(Request $request)
    {
       //validate inputs
       $validate=$request->validate([
        'ProviderNameUI'=>'required',
        'ProviderUserNameUI'=>'required',
        'ProviderIconSrcUI'=>'required',
        'ProviderDescUI'=>'required',
        'UpdateProviderId'=>'required'
       ]);
       
       //Check Provider And Update
       $getProvider=MayarProvider::find($validate['UpdateProviderId']);
       if(!empty($getProvider)){

       //Update Provider
       $getProvider->update([
           'ProviderName'=>$validate['ProviderNameUI'],
           'ProviderUserName'=>$validate['ProviderUserNameUI'],
           'ProviderIconSrc'=>$validate['ProviderIconSrcUI'],
           'ProviderDesc'=>$validate['ProviderDescUI']
       ]);

       return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Provider Succesfully Updated']);

       }
       else{
        return redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
       }

    }

    public function ProviderOne(Request $request)
    {
        //validate inputs
        $validate=$request->validate([
            'ProviderId'=>'required'
        ]);

        //Check And get Provider
        $getProvider=MayarProvider::find($validate['ProviderId']);
        if(!empty($getProvider)){
        return response()->json($getProvider, 200);
        }
        else{
            return response()->json('Somthing Wrong',404);
        }
    
    }

    public function DelProvider(Request $request)
    {

                //validate Inputs
                $validate=$request->validate([
                    'BigBossPassI'=>'required',
                    'ProviderId'=>'required'
                ]);
        
                //Check BigBoss Pass
                if($validate['BigBossPassI'] != env('BIGBOSSP')){
          
                    return redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'BigBoss Passowrd Is Not Valid']);
        
                }
        
                //Check Provider And They have No Services
                $CheckProvider=MayarProvider::find($validate['ProviderId']);
                if(!empty($CheckProvider) && $CheckProvider['ProviderServiceNum'] ==0 ){
                    
                //Delete Provider
                $CheckProvider->delete();
         
                return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Provider Succesfully Deleted']);
                }
                else{
                
                return redirect()->route('ProviderList')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
                }
        return $request->all();
    }


    public function CategoryListGet()
    {

        //get Categories
        $getCategories=MayarCategory::all();

        return view('BigBoss.CategoryList',['Categories'=>$getCategories]);
    }

    public function SaveCategory(Request $request)
    {

        //validate Inputs
        $validate=$request->validate([
            'CategoryNameI'=>'required',
            'CatThumbnailI'=>'required|max:1999',
            'CategoryCoverI'=>'required:max:1999',
            'CategoryDescI'=>'required'
        ]);

        //Check If Category Is Available
        $CheckCategory=MayarCategory::where('CategoryName',$validate['CategoryNameI'])->count();

        if($CheckCategory >1){
            return redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'Category Is Already In Use']);
        }

        //Upload Thumbnail
        set_time_limit(300);
        $dir=config('getEnv.CatThumbFolder'); //Category Thumbnails
        $dirC=config('getEnv.CatCoverFolder'); //Category Cover
       //Check Input
       if($request->hasFile("CatThumbnailI")){

        //get Orgenal FIle Name 
        $OFilename=$request->file('CatThumbnailI')->getClientOriginalName();
       
       //Upload File To Cache Folder 
        $filename=Storage::cloud()->put($dir,$request->file("CatThumbnailI"),file_get_contents($request->file('CatThumbnailI')));
    
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

        //Upload Category Cover
        if($request->hasFile("CategoryCoverI")){

            //get Orgenal FIle Name 
            $COFilename=$request->file('CategoryCoverI')->getClientOriginalName();
           
           //Upload File To Cache Folder 
            $Cfilename=Storage::cloud()->put($dirC,$request->file("CategoryCoverI"),file_get_contents($request->file('CategoryCoverI')));
        
            //get Uploaded File BaseName 
            $recursive = false; // Get subdirectories also?
            $Ccontents = collect(Storage::cloud()->listContents($dirC, $recursive));
            $Cfile = $Ccontents
                ->where('type', '=', 'file')
                ->where('filename', '=', pathinfo($Cfilename, PATHINFO_FILENAME))
                ->where('extension', '=', pathinfo($Cfilename, PATHINFO_EXTENSION))
                ->first(); // there can be duplicate file names! 
    
            //Set Permission
            $Cservice = Storage::cloud()->getAdapter()->getService();
            $CpermissionService = new \Google_Service_Drive_Permission();
            $CpermissionService->role = "reader";
            $CpermissionService->type = "anyone"; // anyone with the link can view the file
            $Cservice->permissions->create($Cfile['basename'], $CpermissionService);
        }
        //Save Category
        $SaveCategory=new MayarCategory([
            'CategoryName'=>$validate['CategoryNameI'],
            'CategoryThumb'=>$file['basename'],
            'CategotyCover'=>$Cfile['basename'],
            'CategoryDesc'=>$validate['CategoryDescI'],
            'CategoryServiceNum'=>0
        ]);
        $SaveCategory->save();

        return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Category Succesfully Saved']);
    }

    public function DelCategory(Request $request)
    {

        //validate Inputs
        $validate=$request->validate([
            'BigBossPassI'=>'required',
            'CatId'=>'required'
        ]);

        //Check BigBoss Pass
        if($validate['BigBossPassI'] != config('getEnv.BigBossPass')){
            return redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'BigBoss Passowrd Is Not Valid']);
        }


        //Check Category And They have No Services
        $CheckCategory=MayarCategory::find($validate['CatId']);
        if(!empty($CheckCategory) && $CheckCategory['CategoryServiceNum'] ==0 ){

        //get Thumbnail And Remove It
        $dir=config('getEnv.CatThumbFolder');
        //get Uploaded File BaseName 

        $dirC=config('getEnv.CatCoverFolder'); //Category Cover

       //Delete Old File 
        $recursive = false; // Get subdirectories also?
        $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
        $Rmfile = $Rmcontents
            ->where('type', '=', 'file')
            ->where('basename', '=',$CheckCategory['CategoryThumb'])
            ->first();
        if(!empty($Rmfile)){

            //delete Old Thumbnail On Cloud
            Storage::delete($CheckCategory['CategoryThumb']);  

        }

        //Delete Category Cover
        $CRmcontents = collect(Storage::cloud()->listContents($dirC, $recursive));
        $CRmfile = $CRmcontents
            ->where('type', '=', 'file')
            ->where('basename', '=',$CheckCategory['CategoryCover'])
            ->first();
        if(!empty($Rmfile)){

            //delete Old Thumbnail On Cloud
            Storage::delete($CheckCategory['CategoryCover']);  

        }



        //Delete Category
        $CheckCategory->delete();
 
        return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Category Succesfully Deleted']);
        }
        else{
        
        return redirect()->route('CategoryList')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
        }

    }

    public function CategoryOne(Request $request)
    {
        //validate Input
        $validate=$request->validate([
            'CatId'=>'required'
        ]);

        //Chcek Category
        $getCategory=MayarCategory::find($validate['CatId']);

        if(!empty($getCategory)){
            
        return response()->json($getCategory, 200);

        }else{
            return response()->json('Somthing Wrong',404);
        }

    }

    public function UpdateCategory(Request $request)
    {

        set_time_limit(300);
        //validate Inputs
        $validate=$request->validate([
            'CategoryNameUpdateI'=>'required',
            'UpdateCatId'=>'required',
            'CategoryDescUpdateI'=>'required',
            'CatThumUpI'=>'max:1999',
            'CatCoverUpI'=>'max:1999'
        ]);

        //Check Category And Update 
        $getCategory=MayarCategory::find($validate['UpdateCatId']);

        if(!empty($getCategory)){
        
             //Check If Update Have New File 
             if($request->hasFile("CatThumUpI")){

                //Upload New File
                  
                  //get Orgenal FIle Name 
                  $OFilename=$request->file('CatThumUpI')->getClientOriginalName();
              
  
                  //Upload File To Service Thumb Folder 
                  $dir=config('getEnv.CatThumbFolder');
                  $filename=Storage::cloud()->put($dir,$request->file("CatThumUpI"),file_get_contents($request->file('CatThumUpI')));
                  
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
                      ->where('basename', '=',$getCategory['CategoryThumb']);
  
                  //delete Old Thumbnail On Cloud
                  Storage::delete($getCategory['CategoryThumb']);  
  
  
                //Update ServiceThumb On DB
                  $getCategory->update([
                      'CategoryThumb'=>$file['basename']
                  ]);
                  
              }

              
            //Upload Category Cover
            if($request->hasFile("CatCoverUpI")){

                $dirC=config('getEnv.CatCoverFolder'); //Category Cover

                //get Orgenal FIle Name 
                $COFilename=$request->file('CatCoverUpI')->getClientOriginalName();
            
            //Upload File To Cache Folder 
                $Cfilename=Storage::cloud()->put($dirC,$request->file("CatCoverUpI"),file_get_contents($request->file('CatCoverUpI')));
            
                //get Uploaded File BaseName 
                $recursive = false; // Get subdirectories also?
                $Ccontents = collect(Storage::cloud()->listContents($dirC, $recursive));
                $Cfile = $Ccontents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($Cfilename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($Cfilename, PATHINFO_EXTENSION))
                    ->first(); // there can be duplicate file names! 
        
                //Set Permission
                $Cservice = Storage::cloud()->getAdapter()->getService();
                $CpermissionService = new \Google_Service_Drive_Permission();
                $CpermissionService->role = "reader";
                $CpermissionService->type = "anyone"; // anyone with the link can view the file
                $Cservice->permissions->create($Cfile['basename'], $CpermissionService);

                //Delete Old File 
                $recursive = false; // Get subdirectories also?
                $CRmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
                $CRmfile = $CRmcontents
                    ->where('type', '=', 'file')
                    ->where('basename', '=',$getCategory['CategotyCover']);

                //delete Old Thumbnail On Cloud
                Storage::delete($getCategory['CategotyCover']);  


                //Update ServiceThumb On DB
                $getCategory->update([
                    'CategotyCover'=>$Cfile['basename']
                ]);
            }


        //Update Category
        $getCategory->update([
            "CategoryName"=>$validate['CategoryNameUpdateI'],
            'CategoryDesc'=>$validate['CategoryDescUpdateI'],
        ]);
        return redirect()->route('DashboardGet')->with('err',['err'=>'1','message'=>'Category Succsesfully updated']);
        }
        else{
        return redirect()->route('DashboardGet')->with('err',['err'=>'0','message'=>'Somthing Wrong']);
        }
        
    }


    public function MonthlyReportsGet()
    {
        //

        //get Monthly Reports 
        $getReport=MayarReport::where('ReportType','Monthly')->get();

        return view('BigBoss.ReportsList',['Reports'=>$getReport]);
    }

    public function DailyReportsGet()
    {
        //

        //get Daily Reports

        $getReport=MayarReport::where('ReportType','Daily')->get();

        return view('BigBoss.ReportsListDaily',['Reports'=>$getReport]);
    }


    public function TeamWorkGet()
    {
        //

        //get Team
        $getTeam=MayarTeam::all();

        return view('BigBoss.TeamWork',['Emps'=>$getTeam]);

    }


    public function SaveEmployee(Request $request)
    {
        
        //Validate inputs 
        $validate=$request->validate([
            'EmpNameI'=>'required',
            'EmpPositionI'=>'required',
            'EmpThumbI'=>'max:1999'
        ]);

        //Check If have File
        if($request->hasFile("EmpThumbI")){

            //Upload File To Drive 
            $dir=config('getEnv.EmpThumbFolder');

            //get Orgenal FIle Name 
            $OFilename=$request->file('EmpThumbI')->getClientOriginalName();
           
           //Upload File To Cache Folder 
            $filename=Storage::cloud()->put($dir,$request->file("EmpThumbI"),file_get_contents($request->file('EmpThumbI')));
        
            //get Uploaded File BaseName 
            $recursive = false; // Get subdirectories also?
            $contents = collect(Storage::cloud()->listContents($dir, $recursive));
            $file = $contents
                ->where('type', '=', 'file')
                ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                ->first(); // there can be duplicate file names! 
      
                $EmpThumb=$file['basename'];
        }
        else{

            $EmpThumb='13CeDYSUtG46OQphkOIsS6U1ck_vppr2_';

        }

        //Save Employee
        // 					
        $SaveEmployee=new MayarTeam([
            'Name'=>$validate['EmpNameI'],
            'Thumb'=>$EmpThumb,
            'Position'=>$validate['EmpPositionI'],
            'FaceBook'=>$request->input('EmpFaceBookI'),
            'LinkedIn'=>$request->input('EmpLinkedInI')
        ]);

        $SaveEmployee->save();

        return redirect()->route('TeamWorkGet')->with('err',['err'=>'1','message'=>'Employee Succsessfully Created']);

    }

    public function getEmpAj(Request $request)
    {
        //validate inputs
        $validate = Validator::make(request()->all(), [
            'empId'=>'required',
        ]);
        

        if ($validate->fails()) {
            return response()->json(400);
        }

        //get Employee 
        $getEmp=MayarTeam::find($request->input('empId'));

        if(!empty($getEmp)){

            return response()->json($getEmp, 200);

        }
        else{
            return response()->json(400);
        }

    }

    public function UpdateEmp(Request $request)
    {
        //Validate Inputs 
        $validate=$request->validate([
            'EmpNameUpI'=>'required',
            'EmpPositionUpI'=>'required',
            'EmpFaceBookUpI'=>'required',
            'EmpLinkedInUpI'=>'required',
            'EmpIdUpI'=>'required',
            'EmpThumbUpI'=>'max:1999'
        ]);

        //Find emp By Id
        $getEmp=MayarTeam::find($validate['EmpIdUpI']);
        if(!empty($getEmp)){

        //Check IF have New Thumb

        if($request->hasFile("EmpThumbUpI")){

            //Upload New File
                
                //get Orgenal FIle Name 
                $OFilename=$request->file('EmpThumbUpI')->getClientOriginalName();
            

                //get EmpThumb Folder 
                $dir=config('getEnv.EmpThumbFolder');
                $filename=Storage::cloud()->put($dir,$request->file("EmpThumbUpI"),file_get_contents($request->file('EmpThumbUpI')));
                
                //get Uploaded File BaseName 
                $recursive = false; // Get subdirectories also?
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                    ->first(); // there can be duplicate file names! 


            //Delete Old File 
                $recursive = false; // Get subdirectories also?
                $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
                $Rmfile = $Rmcontents
                    ->where('type', '=', 'file')
                    ->where('basename', '=',$getEmp['EmpThumb']);

                if(!empty($Rmfile)){
                  //delete Old Thumbnail On Cloud
                  Storage::delete($getEmp['EmpThumb']);  
                }



            //Update ServiceThumb On DB
                $getEmp->update([
                    'EmpThumb'=>$file['basename']
                ]);
                
            }

            //Update Employee
 
            $updateEmp=$getEmp->update([
                'Name'=>$validate['EmpNameUpI'],
                'Position'=>$validate['EmpPositionUpI'],
                'FaceBook'=>$validate['EmpFaceBookUpI'],
                'LinkedIn'=>$validate['EmpLinkedInUpI']
            ]);

            return redirect()->route('TeamWorkGet')->with('err',['err'=>'1','message'=>'Employee Succesfylly Updated']);

        }
    }


    public function DelEmp(Request $request)
    {
        //validate Inputs 
        $validate=$request->validate([
            'BigBossPassI'=>"required",
            'EmpIdDelI'=>'required'
        ]);
        
        //Find Employee 
        $getEmp=MayarTeam::find($validate['EmpIdDelI']);
        if(!empty($getEmp) && $validate['BigBossPassI'] === config('getEnv.BigBossPass')){

            //Remove Employee
            $getEmp->delete();

            return redirect()->route('TeamWorkGet')->with('err',['err'=>'1','message'=>'Employe Successfully Deleted']);
        }
        else{
            return redirect()->route('TeamWorkGet')->with('err',['err'=>'0','message'=>'Wrong Password']);
        }
    }

    public function ProjListGet()
    {
        //get Projects
        $getProj=MayarProj::all();

        return view('BigBoss.ProjList',['Projs'=>$getProj]);
    }

    public function SaveProj(Request $request)
    {
        //validate inptuts 
        $validate=$request->validate([
            'ProjTitleI'=>'required',
            'ProjDescI'=>'required',
            'ProjThumbI'=>'max:1999'
        ]);

        //Upload Project Thumbnail
            
        set_time_limit(300);
        $dir=config('getEnv.ProjThumbFolder'); //Category Thumbnails

        //Check Input
        if($request->hasFile("ProjThumbI")){

        //get Orgenal FIle Name 
        $OFilename=$request->file('ProjThumbI')->getClientOriginalName();
        
        //Upload File To Cache Folder 
        $filename=Storage::cloud()->put($dir,$request->file("ProjThumbI"),file_get_contents($request->file('ProjThumbI')));
    
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
            $file['basename']='Default Proj';
        }

        //Save Projects
        $SaveProj=new MayarProj([
            'ProjTitle'=>$validate['ProjTitleI'],
            'ProjDesc'=>$validate['ProjDescI'],
            'ProjThumb'=>$file['basename']
        ]);
        $SaveProj->save();

        return redirect()->route('ProjListGet')->with('err',['err'=>'1','message'=>'Project Succesfully Created']);

    }

    public function DelProj(Request $request)
    {
        //validate inputs 
        $validate=$request->validate([
            'ProjId'=>'required',
            'BigBossPassI'=>'required'
        ]);


        //Check BigBoss Password
        if($validate['BigBossPassI'] != config('getEnv.BigBossPass') ){
            return redirect()->route('ProjListGet')->with('err',['err'=>'0','message'=>'BigBoss Worng Password']);
        }

        //Check Proj
        $CheckProj=MayarProj::find($validate['ProjId']);

        if(empty($CheckProj)){
            return redirect()->route('ProjListGet')->with('err',['err'=>'0','message'=>'Cant Find Project']);
        }


        //Remove Proj Thumb
        $dir=config('getEnv.ProjThumbFolder');
        $recursive = false; // Get subdirectories also?
        $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
        $Rmfile = $Rmcontents
            ->where('type', '=', 'file')
            ->where('basename', '=',$CheckProj['ProjThumb'])
            ->first();
        if(!empty($Rmfile)){

        //delete Old Thumbnail On Cloud
        Storage::delete($CheckProj['ProjThumb']);  
        }

        //Remove Proj
        $CheckProj->delete();

        return redirect()->route('ProjListGet')->with('err',['err'=>'1','message'=>'Project Successfully Deleted']);
    }


    public function getProjAj(Request $request)
    {
        //Validate Inputs 
        $validate = Validator::make(request()->all(), [
            'ProjId'=>'required',
        ]);
        

        if ($validate->fails()) {
            return response()->json(400);
        }
        
        //Check Proj
        $CheckProj=MayarProj::find($request->input('ProjId'));

        if(!empty($CheckProj)){
            return response()->json($CheckProj, 200);
        }
        else{
            return response()->json(400);
        }

        //Done
    }

    public function UpdateProj(Request $request)
    {
        //Validate Inputs 
        $validate=$request->validate([
            'ProjTitleUI'=>'required',
            'ProjDescUI'=>'required',
            'ProjIdUI'=>'required',
            'ProjThumbUI'=>'max:1999'
        ]);
        

        //Check Proj
        $CheckProj=MayarProj::find($validate['ProjIdUI']);

        if(empty($CheckProj)){
            return redirect()->route('ProjListGet')->with('err',['err'=>'0','message'=>'Cant Find Project']);
        }

        //Check If Update Have New File 
        if($request->hasFile("ProjThumbUI")){

            //Upload New File
                
                //get Orgenal FIle Name 
                $OFilename=$request->file('ProjThumbUI')->getClientOriginalName();
            

                //Upload File To Service Thumb Folder 
                $dir=config('getEnv.ProjThumbFolder');
                $filename=Storage::cloud()->put($dir,$request->file("ProjThumbUI"),file_get_contents($request->file('ProjThumbUI')));
                
                //get Uploaded File BaseName 
                $recursive = false; // Get subdirectories also?
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                    ->first(); // there can be duplicate file names! 


            //Delete Old File 
                $recursive = false; // Get subdirectories also?
                $Rmcontents = collect(Storage::cloud()->listContents($dir, $recursive));
                $Rmfile = $Rmcontents
                    ->where('type', '=', 'file')
                    ->where('basename', '=',$CheckProj['ProjThumb']);

                if(!empty($Rmfile)){
                  //delete Old Thumbnail On Cloud
                  Storage::delete($CheckProj['ProjThumb']);  
                }



            //Update ServiceThumb On DB
                $CheckProj->update([
                    'ProjThumb'=>$file['basename']
                ]);
                
            }

        //Update proj
        $CheckProj->update([
            'ProjTitle'=>$validate['ProjTitleUI'],
            'ProjDesc'=>$validate['ProjDescUI']
        ]);

        return redirect()->route('ProjListGet')->with('err',['err'=>'1','message'=>'Project Succesfully Updated']);
        //Done
    }

    public function MessageGet()
    {

        //get Ch
        $getCh=MessageCh::where('ProviderId',0)->get();
        if(!empty($getCh)){
            $getCh->load('Messages');
            $getCh->load('Customer');
        }
        
        return view('BigBoss.Messages',['Contacts'=>$getCh]);
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
                'MessageSource'=>0,
                'MessageValue'=>$validate['MsgValueI'],
                'MessageStatus'=>0,
                //'MessageOrderId'=>$validate['MessageOrderIdI'],
                'MessageTargetType'=>2,
                'MessageSourceType'=>0,
                'MessageCh'=>$getCh['id']
            ]);

            $SaveMsg->save();
        }
        else{
            return 'No Ch';
        }
    
    }


}
