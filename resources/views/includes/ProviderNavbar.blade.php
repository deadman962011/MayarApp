<nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        
        <button type="button" class="navbar-toggle SideNavBtn" >
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"></a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
      @if( str_replace('_','-',app()->getLocale()) == 'ar' )
        <ul class="nav navbar-nav navbar-left">
      @else
        <ul class="nav navbar-nav navbar-right">
      @endif
          <li class="dropdown">
            <a class="dropdown-toggle NotifDrop" data-toggle="dropdown" data-type='Provider' href="#"><span class=" 	glyphicon glyphicon-bell"></span></a>
            <ul class="dropdown-menu" style="overflow-y: overlay;max-height: 300px;width:300px">
              @if (!empty($ProviderNotifs))
                @foreach ($ProviderNotifs as $PNotif)

                @if ($PNotif['NotifStatus'] == 0)
                 <li style="background-color:#e8e8e8">
                @else
                 <li>
                @endif
                  <a href="#">
                    <div>
                      <strong>John Smith</strong>
                      <span class="pull-right text-muted">
                        <em>Yesterday</em>
                      </span>
                    </div>
                    <p>{{$PNotif['NotifValue']}}</p>
                  </a>
                </li>
                <li class="divider"></li>
                @endforeach
              @endif
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle ToastTest MessageDrop" data-toggle="dropdown" data-type='Provider' href="#"><span class='glyphicon glyphicon-envelope'></span></a>
            <ul class="dropdown-menu" style="overflow-y: overlay;max-height: 300px;width:400px">
              @if (!empty($ProviderMessage))
                @foreach ($ProviderMessage as $PMessage)
                 <li>
                  <a href="#">
                    <div>
                      <strong>John Smith</strong>
                      <span class="pull-right text-muted">
                        <em>Yesterday</em>
                      </span>
                    </div>
                    <p>{{$PMessage['MessageValue']}}</p>
                  </a>
                </li>
                <li class="divider"></li>
                @endforeach
              @endif
            </ul>
          </li>
          <li><a href="{{ route('ProviderLogOut') }}"><span class="glyphicon glyphicon-off"></span></a></li>
        </ul>
      </div>
    </div>
  </nav>