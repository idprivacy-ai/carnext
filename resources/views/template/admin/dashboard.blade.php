@extends('layouts.admin')
 
@section('content')
<div class="position-relative min-h-100 main_bg min_h_100">
    
    <div class="container-xxl container-fluid">
        <div class="broadcasts_section mx-md-3">
            <div class="row">
                <div class="col col-12">
                    <!-- Page Heading -->
                    <div class="broadcasts_head mb-3">
                        <div class="page_title">
                            <h2>Dashboard</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gx-xl-3 gx-lg-2 mb-4">
                <!-- Total User Registered -->
                @can('manage users')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('userlist.index') }}" class="dash_card_bg">
                            <div class="position-relative dash_content bg-white shadow-sm rounded p-3 p-lg-2 p-xl-2 py-xl-3">
                                <p><i class="fa-solid fa-user-plus me-2"></i>Total User Registered<span class="cursor-pointer ms-auto" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top" data-bs-content="lorem ipsum"><i class="fa-solid fa-circle-info"></i></span></p>
                                <h2 class="mb-0">{{ $users }}</h2>
                            </div>
                        </a>
                    </div>
                </div>
                @endcan
                @can('manage dealers')

                <!-- Total Dealer Registered -->
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('dealerlist.index') }}" class="dash_card_bg">
                            <div class="position-relative dash_content bg-white shadow-sm rounded p-3 p-lg-2 p-xl-2 py-xl-3">
                                <p><i class="fa-solid fa-user-plus me-2"></i>Total Dealer Registered<span class="cursor-pointer ms-auto" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top" data-bs-content="lorem ipsum"><i class="fa-solid fa-circle-info"></i></span></p>
                                <h2 class="mb-0">{{ $dealer }}</h2>
                            </div>                          
                        </a>
                    </div>
                </div>
                @endcan
                @can('Manage Demo Request')

                <!-- Total Cars -->
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('reqeust.index') }}" class="dash_card_bg">
                            <div class="position-relative dash_content bg-white shadow-sm rounded p-3 p-lg-2 p-xl-2 py-xl-3">
                                <p><i class="fa-solid fa-desktop me-2"></i>Request Demo<span class="cursor-pointer ms-auto" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top" data-bs-content="lorem ipsum"><i class="fa-solid fa-circle-info"></i></span></p>
                                <h2 class="mb-0">{{ $demo }}</h2>
                            </div>                          
                        </a>
                    </div>
                </div>
                @endcan
                @can('manage leads')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('lead.index') }}" class="dash_card_bg">
                            <div class="position-relative dash_content bg-white shadow-sm rounded p-3 p-lg-2 p-xl-2 py-xl-3">
                                <p><i class="fa-solid fa-chart-line me-2"></i>Total Lead<span class="cursor-pointer ms-auto" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top" data-bs-content="lorem ipsum"><i class="fa-solid fa-circle-info"></i></span></p>
                                <h2 class="mb-0">{{ $lead }}</h2>
                            </div>                          
                        </a>
                    </div>
                </div>
                @endcan

            </div>

            <!-- Total Patient Record -->
            <div class="row mb-4">
                <div class="col col-12">
                    <div class="dash_heading">
                        <h4>Quick Navigation</h4>
                    </div>
                </div>
                @can('manage dealers')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('dealerlist.index') }}#dealerForm" class="btn btn-outline-primary w-100"><i class="fa-solid fa-handshake"></i><span class="vr"></span>Add New Dealer</a>
                    </div>
                </div>
                @endcan
                @can('manage stores')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('admin.stores') }}#storesForm" class="btn btn-outline-primary w-100"><i class="fa-solid fa-store"></i><span class="vr"></span>Add New Store</a>
                    </div>
                </div>
                @endcan
                @can('manage roles')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('admin.role') }}#rolesForm" class="btn btn-outline-primary w-100"><i class="fa-solid fa-gear"></i><span class="vr"></span>Create New Role</a>
                    </div>
                </div>
                @endcan

                @can('Manage Employee')
                <div class="col col-lg-3 col-md-6 col-12 mb-3">
                    <div class="position-relative dash_card">
                        <a href="{{ route('admin.employee') }}#employeesForm" class="btn btn-outline-primary w-100"><i class="fa-solid fa-user-plus"></i><span class="vr"></span>Add Employee</a>
                    </div>
                </div>
                @endcan
             
                </div>
        </div>
    </div>
    
</div>
@endsection

@push ('after-scripts')

@endpush
