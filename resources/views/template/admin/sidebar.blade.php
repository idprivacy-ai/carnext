<div id="sideNav" class="d-flex flex-column flex-shrink-0 sidebar_main">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none sidebar_logo">
        @if(isset($setting['website_logo']) && !empty($setting['website_logo'])) 
            <img src="{{ $setting['website_logo'] }}" alt="{{ $setting['website_logo'] }}">
        @else 
            <img src="{{asset('assets/images/logo-b.png') }}" alt="Logo" >
        @endif
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        @can('manage users')
        <li>
            <a href="{{ route('admin.dashboard') }}" class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}">Dashboard</a>
        </li>
        @endcan

        
       

       

        @can('manage users')
        <li>
            <a href="{{ route('userlist.index') }}" class="nav-link{{ request()->routeIs('userlist.index') ? ' active' : '' }}">Users</a>
        </li>
        @endcan

        @can('manage dealers')
        <li>
            <a href="{{ route('dealerlist.index') }}" class="nav-link{{ request()->routeIs('dealerlist.index') ? ' active' : '' }}">Dealers</a>
        </li>
        @endcan

        @can('manage stores')
        <li>
            <a href="{{ route('admin.stores') }}" class="nav-link{{ request()->routeIs('admin.stores') ? ' active' : '' }}">Manage Stores</a>
        </li>
        @endcan

        @can('manage stores')
        <li>
            <a href="{{ route('admin.cancellation.request.list') }}" class="nav-link{{ request()->routeIs('admin.cancellation.request.list') ? ' active' : '' }}">Cancellation Request</a>
        </li>
        @endcan
        @can('manage roles')
        <li>
            <a href="{{ route('admin.role') }}" class="nav-link{{ request()->routeIs('admin.role') ? ' active' : '' }}">Manage Roles</a>
        </li>
        @endcan

        @can('Manage Employee')
        <li>
            <a href="{{ route('admin.employee') }}" class="nav-link{{ request()->routeIs('admin.employee') ? ' active' : '' }}">Manage Employees</a>
        </li>
        @endcan
        @can('Manage Demo Request')
        <li>
            <a href="{{ route('reqeust.index') }}" class="nav-link{{ request()->routeIs('reqeust.index') ? ' active' : '' }}">Demo Request</a>
        </li>
        @endcan
        

       
        @can('manage transaction')
        <li>
            <a href="{{ route('admin.transaction') }}" class="nav-link{{ request()->routeIs('admin.transaction') ? ' active' : '' }}">Transaction</a>
        </li>
        @endcan


        @can('Manage Blog')
        <li>
            <a href="{{ route('pages.index') }}" class="nav-link{{ request()->routeIs('pages.index') ? ' active' : '' }}">Pages</a>
        </li>
        @endcan
        @can('Manage Blog')
        <li>
            <a href="{{ route('post.index') }}" class="nav-link{{ request()->routeIs('post.index') ? ' active' : '' }}">Posts</a>
        </li>
        @endcan
        @can('Manage Media Page')
        <li>
            <a href="{{ route('media.index') }}" class="nav-link{{ request()->routeIs('media.index') ? ' active' : '' }}">Media</a>
        </li>
        @endcan
        @can('manage ads')
        <li>
            <a href="{{ route('ads.index') }}" class="nav-link{{ request()->routeIs('ads.index') ? ' active' : '' }}">Ads</a>
        </li>
        @endcan
        @can('manage leads')
        <li>
            <a href="{{ route('lead.index') }}" class="nav-link{{ request()->routeIs('lead.index') ? ' active' : '' }}">Leads</a>
        </li>
        @endcan
        @can('Manage Contact Us')
        <li>
            <a href="{{ route('contact.index') }}" class="nav-link{{ request()->routeIs('contact.index') ? ' active' : '' }}">Contact Us</a>
        </li>
        @endcan
        @can('manage anisa')
        <li>
            <a href="{{ route('admin.log') }}" class="nav-link{{ request()->routeIs('admin.log') ? ' active' : '' }}">Anisa Conversations</a>
        </li>
        @endcan
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
            @if(auth('admin')->user()->profile_pic)
                <img src="{{ auth('admin')->user()->profile_pic }}" width="32" height="32" class="rounded-circle me-2">
            @else
                <img src="{{ asset('assets/admin/images/avatar.png') }}" alt="" width="32" height="32" class="rounded-circle me-2">
            @endif
            <strong>{{ auth('admin')->user()->name }}</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
            @can('Manage Website Branding & Social')
            <li><a class="dropdown-item" href="{{ route('admin.setting') }}">Setting</a></li>
            @endcan
            <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="get" action="{{ route('admin.logout') }}">
                    @csrf
                    <a href="login" onclick="event.preventDefault(); this.closest('form').submit();">
                        <button class="btn btn-sm btn-white mb-0 me-1" type="submit">Log out</button>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</div>
