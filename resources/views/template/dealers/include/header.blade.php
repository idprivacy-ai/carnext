<header class="main_header" id="main_header">
    <nav class="navbar">
        <div class="container-fluid">
            <a href="{{route('dealer.index')}}" class="navbar-brand order-md-1">
                <img src="{{asset('assets/images/logo.png') }}" alt="CarNext.autos" class="logo_light">
                <img src="{{asset('assets/images/logo-b.png') }}" alt="CarNext.autos" class="logo_dark">
            </a>
            <div class="nav_auth order-md-3">               
                @if(auth()->guard('dealer')->check())
                    <a href="{{ route('dealer.dashboard') }} " class="nav_auth_login"><i class="fa-regular fa-user me-lg-3 me-2"></i>Hello {{ auth('dealer')->user()->first_name }}</a>
                @else
                    <a data-bs-toggle="modal" data-bs-target="#userLoginModal" class="nav_auth_login"><i class="fa-regular fa-user me-lg-3 me-2"></i>Login / Signup</a>
                @endif
                <a href="{{route('home')}}" class="btn nav_dealerbtn" >Consumer</a>
                
            </div>
            @if(auth()->guard('dealer')->check())
            
                @if(isset($subscribedStoreList) &&count($subscribedStoreList))
                    <div class="header_paymsg order-md-2">
                        <a href="javascript:;" onclick="storesubscriptionenable('{{ route('dealer.cart') }} ',`{{ json_encode($subscribedStoreList) }}`)" class="mb-0"><i class="fa-regular fa-triangle-exclamation me-2"></i>Complete your payment to avail the premium subscription features!</a>
                    </div> 
                
                @elseif(isset($unsubscribedStoreList) && count($unsubscribedStoreList))
                    <div class="header_paymsg order-md-2">
                        <a href="javascript:;" onclick="storesubscriptionenable('{{ route('dealer.cart') }} ',`{{ json_encode($unsubscribedStoreList) }}` )" class="mb-0"><i class="fa-regular fa-triangle-exclamation me-2"></i>Complete your subscription payment for other store  premium features!</a>
                    </div> 
               
                
                @endif
            @endif
        </div>
    </nav>
</header>