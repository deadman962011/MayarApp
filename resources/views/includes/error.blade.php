@if (count($errors) > 0)
    @foreach ($errors->all() as $error)
        <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-2 ErrorSign">
            <div class="alert alert-danger">
                {{$error}}
            </div>
        </div>
    @endforeach
@endif


@if (!empty(session()->get('err')))
    @if (session()->get('err')['err'] =="0")
    <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-2 ErrorSign">
        <div class="alert alert-danger">
            <h4 class='text-center'>{{ session()->get('err')['message'] }}</h4>
        </div>
    </div> 
    @endif

    @if (session()->get('err')['err'] =="1")
    <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-2 ErrorSign">
        <div class="alert alert-success">
            <h4 class='text-center'>{{ session()->get('err')['message'] }}</h4>
        </div>
    </div> 
    @endif
@endif