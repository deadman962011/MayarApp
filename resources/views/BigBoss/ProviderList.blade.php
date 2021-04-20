@extends('layout.master')
@extends('includes.datatables')

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')
<div id='Content'>
    <table id='dataTable' class="table table-responsive ">
        <thead>
            <tr>
             <th> Img</th>
             <th>{{ trans('lang.ProviderName') }}</th>
             <th>{{ trans('lang.ProviderSerCount')}}</th>
             <th>{{ trans('lang.Options')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Providers as $Provider)
                <tr>
                    <td>img</td>
                    <td>{{$Provider['ProviderName']}}</td>
                    <td>{{$Provider['ProviderServiceNum']}}</td>
                    <td>
                      <button class="btn btn-warning UpdateProviderBut" data-toggle="modal" data-target="#UpdateProvider" data-providerid='{{$Provider["id"]}}'><span class="glyphcion glyphicon-eye">Update</span></button>
                      <button class="btn btn-danger DelProviderBut" data-toggle="modal" data-target="#DelProvider" data-providerid='{{$Provider["id"]}}'><span class="glyphcion glyphicon-eye">Delete</span></button>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>


<!--UpdateProvider Modal -->
<div id="UpdateProvider" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.UpdateProviderModalTitle') }}</h4>
        </div>
        <div class="modal-body">
          <div class="sk-chase UpdateProviderSpinner">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
          </div>
  
  
  
          <form method="post" action="{{ route('UpdateProvider') }}" class="form-horizontal UpdateProviderForm" >
  
            <!-- Provider Name Update Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ProviderName') }}" name="ProviderNameUI" class="form-control"></div>
            </div>
  
            <!-- Provider Username Update Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ProviderUserName') }}" name="ProviderUserNameUI" class="form-control"></div>
            </div
  
            <!-- Provider Icon Source Update Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="Provider Icon Source" name="ProviderIconSrcUI" class="form-control"></div>
            </div>
  
            <!-- Provider Descreption Update Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><textarea name="ProviderDescUI" class='form-control' cols="30" rows="10" placeholder="Provider Descreption"></textarea></div>
            </div>
            <input type="hidden" name="UpdateProviderId">
         {{ csrf_field() }}
  
        </div>
        <div class="modal-footer">
          <input type="submit" value="Update" class="btn btn-success">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>
<!-- End UpdateProvider Modal -->


<!--DelProvider  Modal -->
<div id="DelProvider" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.DeleteProviderModalTitle') }}</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('DelProvider') }}" class="form-horizontal">
  
            <!-- BigBoss Pass Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="BigBoss Password" name="BigBossPassI" class="form-control" required></div>
            </div>
            <input type="hidden" name="ProviderId" required>
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Delete Category" class="btn btn-danger">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>
  <!-- End DelProvider Modal -->
    

@endsection


@section('Script')
    <script>
      

//get Provider Data For Update Provider Modal
$(document).on('click','.UpdateProviderBut',function(){
     $('.UpdateProviderSpinner').show();
     $('.UpdateProviderForm').hide(); 
  var UpdateProviderId=$(this).data('providerid');
  $.ajax({
  method:'post',
  url:"{{ route('ProviderOne') }}",
  data:{ProviderId:UpdateProviderId,_token:"{{ csrf_token() }}"},
  success:function(data){     
   $('input[name="ProviderNameUI"]').val(data.ProviderName);
   $('input[name="ProviderUserNameUI"]').val(data.ProviderUserName);
   $('input[name="ProviderIconSrcUI"]').val(data.ProviderIconSrc);
   $('textarea[name="ProviderDescUI"]').val(data.ProviderDesc);
   $('input[name="UpdateProviderId"]').val(UpdateProviderId);

   $('.UpdateProviderSpinner').hide(); 
   $('.UpdateProviderForm').show();

  }
})
})
// End get Provider Data For Update Provider Modal


//get Provider Id for Delete Provider modal

$(document).on('click','.DelProviderBut',function(){
  var ProviderId=$(this).data('providerid');

  $('input[name="ProviderId"]').val(ProviderId);
})

// end get Provider Id for Delete Provider modal


    </script>
@endsection