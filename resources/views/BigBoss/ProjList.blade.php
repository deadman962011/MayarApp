@extends('layout.master')

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')

<div id="ContentBlank">
     <div class="col-sm-4">
         <div class="DBOX" style="min-height: 615px">
            <a href="#" style="color: black" data-toggle="modal" data-target="#AddProj" >
                <div class="text-center">
                    <span class="glyphicon glyphicon-plus" style='font-size:90px;padding:10px' ></span>
                    <h4 class="text-bold">Create New Project</h4>
                </div>
            </a>
         </div>
     </div>
     @foreach ($Projs as $Proj)
     <div class="col-sm-4">
        <div class="DBOX" style="padding: 12px 0 0 0 ;min-height: 300px">
            <h3 class='text-center text-bold'>{{ $Proj['ProjTitle'] }}</h3>
            <img src="http://127.0.0.1/images/proj.png" class="img-responsive" style='padding: 6px; max-height: 516px;' >
            <div class="btn-group ProjOps">
                <button  class="btn btn-warning UpdateProjBtn" data-toggle="modal" data-target="#UpdateProj" data-proj='{{ $Proj['id'] }}' >Update</button>
                <button  class="btn btn-danger DelProjBtn" data-toggle="modal" data-target="#DelProj" data-proj='{{ $Proj['id'] }}'>Delete</button>
            </div>
        </div>
    </div>
     @endforeach    

</div>


<!-- Add Project Modal -->
<div class="modal fade" id="AddProj" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create New Project Modal</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="SaveProj" class="form-horizontal">
              <div class="form-group">
                  <div class="col-sm-10 col-sm-offset-1">
                    <input class="form-control" type="text" name="ProjTitleI" placeholder="Project Title" required>
                  </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-10 col-sm-offset-1">
                      <input type="file" name="ProjThumbI" class="form-control" placeholder="Project Thumbnail" >
                  </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-10 col-sm-offset-1">
                      <div class="form-group">
                          <textarea  class="form-control" name="ProjDescI" rows="3" placeholder="Project Descreptio" required></textarea>
                      </div>
                  </div>
              </div>
      
        </div>
        <div class="modal-footer">
            {{ csrf_field() }}
          <input type="submit" value="Save" class="btn btn-primary">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>


<!-- Update Project Modal -->
<div class="modal fade" id="UpdateProj" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Project Modal</h4>
        </div>
        <div class="modal-body">
          <div class="sk-chase UpdateProjSpinner">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
          </div>

        <form action="{{ route('UpdateProj') }}" method="post" class="form-horizontal UpdateProjForm" >
              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                  <input class="form-control" type="text" name="ProjTitleUI" placeholder="Project Title" required>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                    <input type="file" name="ProjThumbUI" class="form-control" placeholder="Project Thumbnail" >
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                        <textarea  class="form-control" name="ProjDescUI" rows="3" placeholder="Project Descreption" required></textarea>
                        <input type="hidden" name="ProjIdUI">
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
          {{ csrf_field() }}
          <input type="submit" value="Update" class="btn btn-warning">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>


<!-- Delete Project Modal -->
<div class="modal fade" id="DelProj" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Project Modal</h4>
        </div>
        <div class="modal-body">
          <form action="{{ route('DelProj') }}" method="post" class="form-horizontal">
           
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                    <input type="text" name="BigBossPassI" class="form-control" placeholder="BigBoss Password">
                    <input type="hidden" name="ProjId">
                </div>
            </div>

        </div>
        <div class="modal-footer">
            {{ csrf_field() }}
         <input type="submit" value="Delete" class="btn btn-danger">
         </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

  @endsection

  @section('Script')
      
  <script>
 
  $(document).on('click','.DelProjBtn',function(){

    var ProjId=$(this).data('proj');

    console.log(ProjId)

    $.ajax({
        method:"post",
        url:"{{ route('getProjAj') }}",
        data:{
            ProjId:ProjId,
            _token:"{{ csrf_token() }}"
        },
        success:function(data){
         $('input[name="ProjId"]').val(data.id)
        }
    })
  })


  //Update Modal Loading
  $(document).on('click','.UpdateProjBtn',function(){
    $('.UpdateProjSpinner').show();
    $('.UpdateProjForm').hide(); 
   var ProjId=$(this).data('proj');

   $.ajax({
        method:"post",
        url:"{{ route('getProjAj') }}",
        data:{
            ProjId:ProjId,
            _token:"{{ csrf_token() }}"
        },
        success:function(data){
         $('input[name="ProjTitleUI"]').val(data.ProjTitle)
         $('textarea[name="ProjDescUI"]').val(data.ProjDesc)
         $('input[name="ProjIdUI"]').val(data.id)
         $('.UpdateProjSpinner').hide();
         $('.UpdateProjForm').show(); 
        }
    })

  })

  

  </script>


  @endsection   