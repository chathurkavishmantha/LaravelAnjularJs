<div class="leftbar">
    <!-- Start Sidebar -->
    <div class="sidebar">
        <!-- Start Logobar -->
        <div class="logobar">
            <a href="{{url('/')}}" class="logo logo-large"><img src="{{url('assets/images/logo.png')}}" class="img-fluid" alt="logo"></a>
            <a href="{{url('/')}}" class="logo logo-small"><img src="{{url('assets/images/small_logo.png')}}" class="img-fluid" alt="logo"></a>
        </div>
        <!-- End Logobar -->
        <!-- Start Navigationbar -->
        <div class="navigationbar">
            <ul class="vertical-menu">
                <li>
                    <a href="{{url('/')}}">
                        <i class="feather icon-home"></i><span>Dashboard</span></i>
                    </a>
                 
                </li>
                
                @foreach(getMenu() as $menuLabel => $subMenu)
                
                <li>
                    <a href="javaScript:void();">
                        <i class="{{getIcon($menuLabel)}}"></i><span>{{$menuLabel}}</span><i class="feather icon-chevron-right pull-right"></i>
                    </a>
                    <ul class="vertical-submenu">
                        @foreach($subMenu as $subMenuLabel => $url)
                        <li><a href="{{$url}}">{{$subMenuLabel}}</a></li>
                        
                        @endforeach
                        
                    </ul>
                </li>
                
                @endforeach
                
            </ul>
        </div>
        <!-- End Navigationbar -->
    </div>
    <!-- End Sidebar -->
    
    <p class="copyright2">Senska Software Solutions</p>
    
</div>