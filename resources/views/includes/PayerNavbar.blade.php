<nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
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
              <a class="dropdown-toggle NotifDrop" data-toggle="dropdown" data-type='BigBoss' href="#">BigNotif</a>
              <ul class="dropdown-menu">
              </ul>
            </li>
          <li><a href="{{ route('LogOut') }}"><span class="glyphicon glyphicon-off"></span></a></li>
        </ul>
      </div>
    </div>
  </nav>