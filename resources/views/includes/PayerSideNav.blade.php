<div class="wrapper">


    <nav id="sidebar">
      <div class="sidebar-header">
          <h3>BigBoss Sidebar</h3>
      </div>
  
      <ul class="list-unstyled components">
          <li class="active">
          <a href="#OrdersSub" data-toggle="collapse" aria-expanded="false">{{ trans('lang.Orders') }}</a>
              <ul class="collapse list-unstyled" id="OrdersSub">
                <li>  
                    <a href="#Ads" >{{ trans('lang.Advertisment') }}</a>
                </li>
              </ul>
          </li>
          <li>  
           <a href="#Ads" >{{ trans('lang.Advertisment') }}</a>
          </li>
      </ul>
    </nav>
  
    @include('includes.BigBossModals')
  