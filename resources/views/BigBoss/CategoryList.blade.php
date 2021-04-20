@extends('layout.master')
@extends('includes.datatables')

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')
 

    <div id='Content'>
       
            <table id='dataTable' class="table table-responsive">
                <thead>
                    <tr>
                      <th>Img</th>
                     <th>{{ trans('lang.CatName') }}</th>
                     <th>{{ trans('lang.CatSerCount') }}</th>
                    <th>{{ trans('lang.Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Categories as $Category)
                    <tr>
                        <td><img src="https://drive.google.com/thumbnail?id={{$Category['CategoryThumb']}}&sz=w120&sz=h50" alt=""></td>
                        <td>{{ $Category['CategoryName'] }}</td>
                        <td>{{$Category['CategoryServiceNum']}}</td>
                        <td>
                            <button class='btn btn-warning UpdateCatBut'  data-toggle="modal" data-target="#UpdateCategory" data-catid='{{$Category['id']}}'><span class="glyphicon glyphicom-plus"></span>Update</button>
                            <button data-catid='{{$Category['id']}}' data-toggle="modal"  data-target='#DelCategory' class='btn btn-danger CatDelBut'><span class="glyphicon glyphicom-plus"></span>Del</button>
                        </td>
                    </tr>
                    @endforeach
        
                </tbody>
            </table>
       

    </div>

    <!--DelCategory  Modal -->
<div id="DelCategory" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.DeleteCategoryModalTitle') }}</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('DelCategory') }}" class="form-horizontal">
  
            <!-- BigBoss Pass Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="BigBoss Password" name="BigBossPassI" class="form-control" required></div>
            </div>
            <input type="hidden" name="CatId" required>
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Delete Category" class="btn btn-danger">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('lang.Cancel') }}</button>
        </div>
      </div>
  
    </div>
  </div>
  <!-- End DelCategory Modal -->
  
  
  
  
  <!--UpdateCategory  Modal -->
  <div id="UpdateCategory" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.UpdateCategoryModaltitle') }}</h4>
        </div>
        <div class="modal-body">
  
          <div class="sk-chase UpdateCategorySpinner">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
          </div>
  
          <form method="post" action="{{ route('UpdateCategory') }}" class="form-horizontal UpdateCategoryForm" enctype="multipart/form-data">
  
            <!-- Category Thumb Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="file" name="CatThumUpI" class="form-control">
              </div>
            </div>

            <!-- Category Cover Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="file" name="CatCoverUpI" placeholder="Update Category Cover" class="form-control">
              </div>
            </div>

            <!-- Category Name Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.CatName') }}" name="CategoryNameUpdateI" class="form-control"></div>
            </div>

            <!-- Category Descreption Input  -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <textarea class='form-control' name="CategoryDescUpdateI" rows="10"></textarea>
              </div>
            </div>

            <input type="hidden" name="UpdateCatId">
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Update" class="btn btn-success">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('lang.Cancel') }}</button>
        </div>
      </div>
  
    </div>
  </div>
  <!-- End UpdateCategory Modal -->

@endsection

@section('Script')
    
<script>
  
//get Category Id for Delete CategoryModal

$(document).on('click','.CatDelBut',function(){
  var CatId=$(this).data('catid');

  $('input[name="CatId"]').val(CatId);
})

// end get Category Id for Delete CategoryModal



//get Category Data for Update CategoryModal


$(document).on('click','.UpdateCatBut',function(){
  var UpdateCatId=$(this).data('catid');

  $('.UpdateCategorySpinner').show();
  $('.UpdateCategoryForm').hide();

  $.ajax({
  method:'post',
  url:"{{ route('CategoryOne') }}",
  data:{CatId:UpdateCatId,_token:"{{ csrf_token() }}"},
  success:function(data){   

     $('.UpdateCategoryForm').show();
     $('.UpdateCategorySpinner').hide();  
  
    $('input[name="UpdateCatId"]').val(UpdateCatId);
    $('input[name="CategoryNameUpdateI"]').val(data.CategoryName);
    $('textarea[name="CategoryDescUpdateI"]').val(data.CategoryDesc);
  }
})
})


//End get Category Id for Update CategoryModal
</script>
@endsection


