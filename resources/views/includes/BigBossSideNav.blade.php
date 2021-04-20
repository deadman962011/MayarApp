<div class="wrapper">


  <nav id="sidebar">
    <div class="sidebar-header">
        <h3>BigBoss Sidebar</h3>
    </div>

    <ul class="list-unstyled components">
        <li><a href="{{route('DashboardGet')}}">Dashboard</a></li>
        <li class="active">
        <a href="#CategorySub" data-toggle="collapse" aria-expanded="false">{{ trans('lang.Categories') }}</a>
            <ul class="collapse list-unstyled" id="CategorySub">
             <li><a href="{{ route('CategoryList') }}">{{ trans('lang.CategoryList') }}</a></li>
             <li><a href="#"  data-toggle="modal" data-target="#AddCategory">{{ trans('lang.AddCategory') }}</a></li> 
            </ul>
        </li>
        <li>  
        <a href="#ProviderList" data-toggle="collapse" aria-expanded="false">{{ trans('lang.Providers') }}</a>
        <ul class="collapse list-unstyled" id="ProviderList">
            <li><a href="{{ route('ProviderList') }}">{{ trans('lang.ProviderList') }}</a></li>
            <li><a href="#" data-toggle="modal" data-target="#AddProvider">{{ trans('lang.AddProvider') }}</a></li> 
        </ul>
        </li>
        <li>  
         <a href="#ReportsList" data-toggle="collapse" aria-expanded="false" >Reports</a>
         <ul class="collapse list-unstyled" id="ReportsList">
            <li><a href="{{route('MonthlyReportsGet')}}">Monthly Reports</a></li>
            <li><a href="{{ route('DailyReportsGet') }}" >Daily Reports</a></li> 
        </ul>
        </li>
        <li>
            <a href="#OthersList" data-toggle="collapse" aria-expanded="false" >Others</a>
            <ul class="collapse list-unstyled" id="OthersList">
                <li><a href="{{route('TeamWorkGet')}}">Team Work</a></li>
                <li><a href="{{ route('ProjListGet') }}">Projects List</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ route('MessageBGet') }}">Messages</a>
        </li>
    </ul>
  </nav>
  <script>
      var AuthType='BigBoss';
  </script>
  @include('includes.BigBossModals')
