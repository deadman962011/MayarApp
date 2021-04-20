@extends('layout.master')

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')


 <div id='Content' style="background:transparent;padding:0;border-raduis:0;">

    <div class="col-sm-4">
        <div class="DBOX">
            <a href="#" style="color: black" data-toggle="modal" data-target="#AddEmployee" >
                <div class="text-center">
                    <span class="glyphicon glyphicon-plus" style='font-size:90px;padding:10px' ></span>
                    <h4 class="text-bold">Create New Employee</h4>
                </div>
            </a>
        </div>
    </div>

    @foreach ($Emps as $emp)
        <div class="col-sm-4">
            <div class="DBOX" style="padding:12px 0 0 0">
                <img src="http://127.0.0.1/images/avatar.png"  class="img-responsive center-block">
                <h4 class="text-center ">{{$emp['Name']}}</h4>
                <h3 class="text-center">{{$emp['Position']}}</h3>
                <div class="btn-group EmpOpts">
                <button class="btn btn-warning updateEmp" data-toggle="modal" data-target="#UpdateEmp" data-empid='{{$emp["id"]}}' >Update</button>
                    <button class="btn btn-danger delEmp" data-toggle="modal" data-target="#DelEmp" data-empid='{{$emp["id"]}}' >Delete</button>

                </div>
            </div>
        </div>
    @endforeach


    

</div>

<!-- Start AddEmployee Modal -->

<!-- Modal -->
<div class="modal fade" id="AddEmployee" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Employee Modal</h4>
        </div>
        <div class="modal-body">
          
        <form action="{{route('SaveEmployee')}}" method='post' class="form-horizontal" enctype="multipart/form-data">
  
            <!-- Employee Thumbnail Input  -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="file" name="EmpThumbI" placeholder="Employee Thumbnail" class='form-control'>
              </div>
            </div>
  
            <!-- Employee Name Input  -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="text" name="EmpNameI" placeholder="Employee Full Name" class="form-control" required>
              </div>
            </div>
  
            <!-- Employee Position Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="text" name="EmpPositionI" placeholder="Employee Position"  class="form-control" required>
              </div>
            </div>
  
            <!-- Employee FaceBook Link Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="text" name="EmpFaceBookI" placeholder="Employee FaceBook Link"  class="form-control">
              </div>
            </div>
  
             <!-- Employee LinkedIn Link Input -->
             <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="text" name="EmpLinkedInI" placeholder="Employee LinkedIn Link"  class="form-control">
              </div>
            </div>         
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Save" class="btn btn-success">
          </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
  <!-- End Add Employee Modal -->
    


<!-- Update Employee  Modal -->
<div id="UpdateEmp" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update New Employee Modal</h4>
        </div>
        <div class="modal-body">
            <div class="sk-chase  UpdateEmpSpinner">
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
              </div>
            <form action="{{ route('UpdateEmp') }}" method="post" enctype="multipart/form-data" class="form-horizontal UpdateEmpForm ">
                      <!-- Employee Thumbnail Input  -->
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="file" name="EmpThumbUpI" placeholder="Employee Thumbnail" class='form-control'>
            </div>
          </div>

          <!-- Employee Name Input  -->
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="EmpNameUpI" placeholder="Employee Full Name" class="form-control" required>
            </div>
          </div>

          <!-- Employee Position Input -->
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="EmpPositionUpI" placeholder="Employee Position"  class="form-control" required>
            </div>
          </div>

          <!-- Employee FaceBook Link Input -->
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="EmpFaceBookUpI" placeholder="Employee FaceBook Link"  class="form-control">
            </div>
          </div>

           <!-- Employee LinkedIn Link Input -->
           <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="EmpLinkedInUpI" placeholder="Employee LinkedIn Link"  class="form-control">
            </div>
          </div>         
       {{ csrf_field() }}
       <input type="hidden" name="EmpIdUpI">
                
        
        </div>
        <div class="modal-footer">
            <input type="submit" class="btn btn-success" value="Update"> 
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>

  <!-- End UpdateEmp Modal  -->


  <!-- Start DelEmp Modal -->

  <!-- Modal -->
<div id="DelEmp" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Employee Delete Modal</h4>
      </div>
      <div class="modal-body">
        
        <form action="{{ route('DelEmp') }}" method="post" class="form-horizontal">

          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="BigBossPassI" class="form-control" placeholder="BigBoss Password">
            </div>
            <input type="hidden" name="EmpIdDelI">
            {{ csrf_field() }}
          </div>

      </div>

      <div class="modal-footer">
        <input type="submit" value="Delete" class="btn btn-danger">
        </form>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

    </div>
  </div>

  <!-- End DelEmp Modal -->

@endsection


@section('Script')

<script>
  //get Employee Data For Update Modal

  $(document).on('click','.updateEmp',function(){
      var EmployeeId= $(this).data('empid');

      
      $('.UpdateEmpSpinner').show();
      $('.UpdateEmpForm').hide();
      $.ajax({
          method:"post",
          url:"{{ route('getEmpAj') }}",
          data:{
              empId:EmployeeId,
              _token:"{{ csrf_token() }}"
          }
      ,success:function(data){

          //Set Value of Inputs 
          
         $('.UpdateEmpForm').show();
         $('.UpdateEmpSpinner').hide();  
        $('input[name="EmpNameUpI"]').val(data.Name)
        $('input[name="EmpPositionUpI"]').val(data.Position)
        $('input[name="EmpFaceBookUpI"]').val(data.FaceBook)
        $('input[name="EmpLinkedInUpI"]').val(data.LinkedIn)
        $('input[name="EmpIdUpI"]').val(data.id)
        }
      })

 })


 // Get Employee To remove
$(document).on('click','.delEmp',function(){

  //get Emp Id
  var EmpId=$(this).data('empid');

  $('input[name="EmpIdDelI"]').val(EmpId)

})

</script>

@endsection