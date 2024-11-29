<div class="offcanvas-collapse h-100" id="mobMenu">
    <div class="account_menu sticky" id="account_menu">
        <div class="account_menu_profile">
            <div class="profile_picture">
                <form id="user_profile_form" action="{{ route('dealer.updateProfilepic') }}">
                    @csrf
                    <div class="position-relative">
                        <div class="circle">
                            @if(auth('dealer')->user()->profile_pic)
                                <img class="profile-pic" src="{{auth('dealer')->user()->profile_pic}}">  
                            @else
                                <img class="profile-pic" src="{{asset('assets/images/prof_pic.jpg') }}">  
                            @endif                                  
                        </div>
                        <div class="p-image">
                            <i class="fa-solid fa-pen upload-button"></i>
                            <input class="file-upload" type="file" name="profile_pic" accept="image/*"/>
                        </div>
                    </div>  
                </form>                                    
            </div>
            <div class="profile_info">
                <h5>{{ auth('dealer')->user()->first_name }} {{ auth('dealer')->user()->last_name }}</h5>
                <p>+ {{ auth('dealer')->user()->phone_number }}</p>
            </div>
        </div>
        <div class="account_menu_list">
            <ul>
                
               
                <li>
                    <a href="{{route('dealer.dashboard')}}" class="page_link {{ request()->routeIs('dealer.dashboard') ? ' active' : '' }}"><i class="fa-regular fa-house me-2 ms-0"></i><span>Dashboard</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
               
                @can('View Store Vehicles', 'dealer')
                <li>
                    <a href="{{route('dealer.myvehicle')}}" class="page_link {{ request()->routeIs('dealer.myvehicle') ? ' active' : '' }}"><i class="fa-regular fa-car me-2 ms-0"></i><span>My Vehicles</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
                @endcan

               

                @can('view lead', 'dealer')
                <li>
                    <a href="{{route('dealer.mylead')}}" class="page_link {{ request()->routeIs('dealer.mylead') ? ' active' : '' }}"><i class="fa-regular fa-filter me-2 ms-0"></i><span>My Leads</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
                @endcan

                @can('manage store', 'dealer')
                <li>
                    <a href="{{route('dealer.stores')}}" class="page_link {{ request()->routeIs('dealer.stores') ? ' active' : '' }}"><i class="fa-regular fa-bag-shopping me-2 ms-0"></i><span>Store</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
                @endcan

                @can('manage role', 'dealer')
                <li>
                    <a href="{{route('dealer.role')}}" class="page_link {{ request()->routeIs('dealer.role') ? ' active' : '' }}"><i class="fa-regular fa-gear me-2 ms-0"></i><span>Role</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
                @endcan

                @can('manage employee', 'dealer')
                <li>
                    <a href="{{route('dealer.employee')}}" class="page_link {{ request()->routeIs('dealer.employee') ? ' active' : '' }}"><i class="fa-regular fa-users me-2 ms-0"></i><span>Employee</span><i class="fa-solid fa-angle-right"></i></a>
                </li>   
                @endcan

                @can('manage dealers', 'dealer')          
                <!--li>
                    <a href="{{route('dealer.subscription')}}" class="page_link {{ request()->routeIs('dealer.subscription') ? ' active' : '' }}"><i class="fa-regular fa-calendar me-2 ms-0"></i><span>My Subscription</span><i class="fa-solid fa-angle-right"></i></a>
                </li-->
                @endcan

                @can('manage dealers', 'dealer')

                <li>
                    <a href="{{route('dealer.payment_method')}}" class="page_link {{ request()->routeIs('dealer.payment_method') ? ' active' : '' }}"><i class="fa-regular fa-credit-card me-2 ms-0"></i><span>Payment Method</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
                @endcan

               
                <li>
                    <a href="{{route('dealer.profile') }}" class="page_link {{ request()->routeIs('dealer.profile') ? ' active' : '' }}"><i class="fa-regular fa-gear me-2 ms-0"></i><span>Profile Settings</span><i class="fa-solid fa-angle-right"></i></a>
                </li>
               



            </ul>
            <hr class="m-0">
            <ul>
                <li>
                    <a data-bs-toggle="modal" data-bs-target="#dealerlogoutConfirmationModal"><i class="fa-regular fa-arrow-right-from-bracket me-2 ms-0"></i><span>Logout</span></a>
                </li>
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close text-reset offcanvas_close" id="mobMenuBtn" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
