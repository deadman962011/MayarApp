<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if($request->is('api/*')){

        

        if (
              $request->is('api/CustRegister')
            ||$request->is('api/CustActivate')
            ||$request->is('api/CustPassRestReq')
            ||$request->is('api/CustPassRestExec')
            ||$request->is('api/CustLogIn')
            ||$request->is('api/ServiceAll/*')
            ||$request->is('api/ServiceOne/*')
            ||$request->is('api/CategoryAll/*')
            ||$request->is('api/CategoryOne/*')
            ||$request->is('api/PayOrderPP')
            ||$request->is('api/PayOrderPPExec')
            ||$request->is('api/PayMayarExec')
            &&  $request->expectsJson()
         ){
           //Do Nothing
         } 
         
         else{
            if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
              
                return response()->json(['err',['err'=>'1','message' => 'TokenInvalidErr']],401);
            }
            else{
            
               // return response()->json(['err',['err'=>'1','message' =>'NotValidRouteErr']],500);
            }
            if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
        
                return response()->json(['err',['err'=>'1','message' => 'TokenExpiredErr']],401);
            }
        }
      }
      return parent::render($request, $exception);
    }
}
