@extends('layout.master')

@section('Style')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/basic.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.css">

@endsection 

@section('Content')
@include('includes.ProviderNavbar')
@include('includes.error')
@include('includes.ProvidersSideNav')
<div id='Content'>
    <div class="panel-group" id="accordion">
        @if (!empty($Orders))
            
        @endif
        @foreach ($Orders as $Order)
        @if ($Order['OrderStatus'] == 2)
         <div class="panel panel-danger">
        @endif
        @if ($Order['OrderStatus'] == 1)
         <div class="panel panel-success">
        @endif
        @if ($Order['OrderStatus'] == 0)
         <div class="panel panel-default">
        @endif
         
            <div class="panel-heading">
              <h4 class="panel-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$Order['id']}}">Order : {{$Order['id']}}</a>
              </h4>
            </div>
            <div id="collapse{{$Order['id']}}" class="panel-collapse collapse ">
              <div class="panel-body">
                  <div class="row">
                      <div class="col-sm-12">
                        <img src="data:image/svg+xml;base64,{{$Order['OrderQr']}}" class='center-block'>
                        <br>
                            <h3>Orderd Service: <span><a href="#">{{ $Order['Service']['ServiceName'] }} </a></span></h3>
                            <h3>Customer: <span><a href='#'> {{ $Order['Customer']['CustUserName'] }} </a></span></h3>
                            <h3>Desception:</h3>
                            <br>
                            <p>{{$Order['OrderDesc']}}</p>
                            <br>
                            <h3>Order Files:</h3>
                            <ul class='OrderFileList'>
                                @foreach ($Order['Files'] as $File)
                                 <li><a href="https://drive.google.com/file/d/{{$File['BaseName']}}"><img src="http://127.0.0.1/images/avatar.png" ><h4>{{ $File['FileName'] }}<span>{{ $File['Ext']}}</span></h4></a></li>
                                @endforeach
                               
                            </ul>
                            <br><br><br>
                            <h3>Biling:</h3>
                            <table class="table table-light">
                                <thead>
                                    <tr>
                                        <th>item</th>
                                        <th>type</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $Order['Service']['ServiceName'] }}</td>
                                        <td>Service</td>
                                        <td>{{ $Order['Service']['ServicePrice']}}</td>
                                    </tr>
                                    @foreach ($Order['OrderUpgradesId'] as $Upgrades)
                                    <tr>
                                        <td>{{ $Upgrades['UpgradeTitle']}}</td>
                                        <td>Service Upgrade</td>
                                        <td>{{ $Upgrades['UpgradePrice']}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>{{ $Order['OrderPrice']}}</th>
                                    </tr>
                                  </tfoot>
                            </table>
                      </div>
                  </div>
              </div>
              <div class="panel-footer">
                  
                <form action="{{route('OrderDeliver')}}" method="post" style="display: inline-block">
                    <input type="hidden" name="OrderIdI" value='{{ $Order['id']}}'>  
                    {{ csrf_field() }}
                    <input type="submit" value="Deliver" class="btn btn-success">
                </form>
                
                <form action="{{route('OrderCancel')}}" method="post" style="display: inline-block">
                    <input type="hidden" name="OrderIdI" value='{{ $Order['id']}}'>  
                    {{ csrf_field() }}
                    <input type="submit" value="Cancel" class="btn btn-danger">
                </form>

                <button type="button" class="btn btn-info UploadFileBtn " data-toggle="modal" data-orderid='{{ $Order['id']}}'  data-target="#FileUplaodModal">Upload File</button>
                <button class="btn btn-primary SendMsgBtn"  data-toggle="modal" data-orderid='{{ $Order['id']}}' data-customerid='{{$Order["Customer"]["id"]}}' data-target="#SendMessage">Send Message </button>
        
             </div>
            </div>
          </div>
        @endforeach

</div>

@endsection


@section('Script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>

@endsection