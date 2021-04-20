
@extends('layout.master')
@section('Content')
@include('includes.error')

<div class="col-xs-12 col-sm-7 col-sm-offset-2 col-md-5 col-md-offset-3">
    <div class="LogInPanel ">
        <h4 class="text-center text-bold">Providers LogIn</h4>
        <br>
        <br>
        <form  method="POST" class="form-horizontal">
            <div class="form-group">
                
                <div class=" col-sm-12"><input name="ProviderUserI" type="text" placeholder="User Name Input" class="form-control"></div>
            </div>
            <div class="form-group">
                
                <div class=" col-sm-12"><input name="ProviderPassI" type="text" placeholder="Password Input" class="form-control"></div>
            </div>
            <br>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4"><input type="submit" value="Log In" class="btn btn-block btn-primary"></div>
            </div>
            {{ csrf_field() }}
        </form>
    </div>
</div>
@endsection

