<nav class="navbar navbar-inverse ">
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

      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
      @if( str_replace('_','-',app()->getLocale()) == 'ar' )
        <ul class="nav navbar-nav navbar-left">
      @else
        <ul class="nav navbar-nav navbar-right">
      @endif
      
            <li class="dropdown">
              <a class="dropdown-toggle NotifDrop" data-toggle="dropdown" data-type='BigBoss' href="#"><span class="glyphicon glyphicon-bell"></span></a>
              <ul class="dropdown-menu">
                @foreach ($BigBossNotifs as $BNotif)
                  <li>
                    <a href="#">
                      <div>
                        <strong>John Smith</strong>
                        <span class="pull-right text-muted">
                          <em>Yesterday</em>
                        </span>
                      </div>
                      <p>{{$BNotif['NotifValue']}}</p>
                    </a>
                  </li>
                  <li class="divider"></li>
                @endforeach
 
                <li>
                  <a class="text-center" href="#">
                    <strong>Read All Messages</strong>
                    <i class="fa fa-angle-right"></i>
                  </a>
                </li>
              </ul>
            </li>
            <li class="dropdown">
              <a class="dropdown-toggle NotifDrop" data-toggle="dropdown" data-type='BigBoss' href="#"><span class="glyphicon glyphicon-envelope"></span></a>
              <ul class="dropdown-menu">
                @foreach ($BigBossNotifs as $BNotif)
                  <li>
                    <a href="#">
                      <div>
                        <strong>John Smith</strong>
                        <span class="pull-right text-muted">
                          <em>Yesterday</em>
                        </span>
                      </div>
                      <p>{{$BNotif['NotifValue']}}</p>
                    </a>
                  </li>
                  <li class="divider"></li>
                @endforeach
 
                <li>
                  <a class="text-center" href="#">
                    <strong>Read All Messages</strong>
                    <i class="fa fa-angle-right"></i>
                  </a>
                </li>
              </ul>
            </li>
          <li><a href="{{ route('LogOut') }}"><span class="glyphicon glyphicon-off"></span></a></li>
        </ul>
      </div>
    </div>
  </nav>
  