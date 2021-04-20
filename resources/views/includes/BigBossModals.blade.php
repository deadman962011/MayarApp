<!--AddCategory  Modal -->
<div id="AddCategory" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.AddCategoryModalTitle') }}</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('SaveCategory') }}" enctype="multipart/form-data" class="form-horizontal">

            <!-- Category Thumbnail Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="file" name="CatThumbnailI" class='form-control'>
              </div>
            </div>

            <!-- Category Cover Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="file" name="CategoryCoverI" class='form-control'>
              </div>
            </div>
            
            <!-- Category Name Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{trans('lang.CatName')}}" name="CategoryNameI" class="form-control"></div>
            </div>

            <!-- Category Descreption Input  -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <textarea class='form-control' name="CategoryDescI" rows="10"></textarea>
              </div>
            </div>
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Save" class="btn btn-primary">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>
<!-- End AddCategory Modal -->

<!--AddProvider  Modal -->
<div id="AddProvider" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{ trans('lang.AddProviderModalTitle') }}</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('SaveProvider') }}" class="form-horizontal">

            <!-- Provider Name Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ProviderName') }}" name="ProviderNameI" class="form-control"></div>
            </div>

            <!-- Provider Username Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ProviderUserName') }}" name="ProviderUserNameI" class="form-control"></div>
            </div>

            <!-- Provider Password Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ProviderPass') }}" name="ProviderPasswordI" class="form-control"></div>
            </div>

            <!-- Provider Icon Source Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="Provider Icon Source" name="ProviderIconSrcI" class="form-control"></div>
            </div>

            <!-- Provider Descreption Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><textarea name="ProviderDescI" class='form-control' cols="30" rows="10" placeholder="Provider Descreption"></textarea></div>
            </div>
            {{ csrf_field() }}
        </div>
        <div class="modal-footer">
            <input type="submit" value="Save" class="btn btn-primary">
          </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('lang.Cancel')}}</button>
        </div>
      </div>
  
    </div>
  </div>
<!-- End AddProvider Modal -->
