@extends('layouts.admin')
 
@section('content')

<!-- Page Content Start -->
<div class="row justify-content-center">
    <div class="col col-xl-8 col-lg-10 col-12">
        <div class="row">
            <div class="col col-12">
                <div class="page_title">
                    <h2>Edit Profile</h2>
                </div>
            </div>        
        </div>

        <hr>

        <div class="admin_inner_content bg_yellow rounded mb-3">
            <div class="row">
                <div class="col col-12">
                    @if (Session::has('profile'))
                        <div class="alert alert-success alert-dismissible text-white" role="alert">
                            <span class="text-sm">{{ Session::get('profile') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10"
                                data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @elseif (Session::has('email'))
                        <div class="alert alert-danger alert-dismissible text-white" role="alert">
                            <span class="text-sm">{{ Session::get('email') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10"
                                data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="col col-12">
                    <form method="POST" action="{{ route('admin.updateProfile') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="position-relative">
                            <div class="position-relative">
                                <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Profile Information
                                </h5>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Update your account's profile information and email address.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col col-lg-6 col-md-6 col-12 mb-3">                            
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="first_name">
                                    First Name
                                    </label>
                                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" name="first_name" value="{{ old('first_name',$user['first_name']) }}" required autocomplete="first_name" autofocus>
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-lg-6 col-md-6 col-12 mb-3">                            
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="last_name">
                                    Last Name
                                    </label>
                                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="last_name" value="{{ old('last_name',$user['last_name']) }}" required autocomplete="last_name" autofocus>
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-lg-6 col-md-6 col-12 mb-3">                            
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="profile_pic">
                                    Profile Picture
                                    </label>
                                    <input id="profile_pic" type="file" class="form-control @error('profile_pic') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="profile_pic" value="{{ old('profile_pic',$user['profile_pic']) }}" autocomplete="profile_pic" autofocus>
                                    @if(isset($user['profile_pic']))
                                    <img src="{{$user['profile_pic']}}" width="100px" height="100px">
                                    @endif
                                    @error('profile_pic')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-lg-6 col-md-6 col-12 mb-3">                            
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="email">
                                    Email
                                    </label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="email" value="{{ old('email',$user['email']) }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-12">                            
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="submit" class="btn btn_theme mx-auto">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>

        <div class="admin_inner_content bg_yellow rounded">
            <div class="row">
                <div class="col col-12">
                    @if (Session::has('password'))
                    <div class="alert alert-success alert-dismissible text-white" role="alert">
                        <span class="text-sm">{{ Session::get('password') }}</span>
                        <button type="button" class="btn-close text-lg py-3 opacity-10"
                            data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                </div>
                <div class="col col-12">
                    <form method="POST" action="{{ route('admin.updatePassword') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="position-relative">
                            <div class="position-relative">
                                <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Update Password
                                </h5>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Ensure your account is using a long, random password to stay secure.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col col-lg-6 col-md-6 col-12 mb-3 ">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="current_password">
                                    Current Password
                                    </label>
                                    <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="current_password" value="{{ old('current_password') }}" autocomplete="current-password">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-lg-6 col-md-6 col-12 mb-3 ">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="new_password">
                                    New Password
                                    </label>
                                    <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="new_password" value="{{ old('new_password') }}" autocomplete="current-password">
                                    @error('new_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-lg-6 col-md-6 col-12 mb-3">
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="confirm_password">
                                    Confirm New Password
                                    </label>
                                    <input id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                    name="confirm_password" value="{{ old('confirm_password') }}" autocomplete="current-password">
                                    @error('confirm_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col col-12">
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="submit" class="btn btn_theme mx-auto">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push ('after-scripts')

@endpush
