@extends('layouts.guest')
 
@section('content')
    <!-- Session Status -->
    <div class="container-fluid h-100 p-0">
        <div class="row justify-content-center g-0 h-100 bg-light">
            <div class="col col-lg-3 col-12 h-100 d-flex align-items-center bg-white px-lg-5">
                <div class="position-relative w-100">
                    <!-- Session Status -->
                    <div class="mb-4 applogo text-center">
                        <a href="/">
                            @if(isset($setting['website_logo']) && !empty($setting['website_logo'])) 
                                <img src="{{ $setting['website_logo'] }}" alt="{{ $setting['website_logo'] }}"  alt="" width="100" height="100" class="rounded-circle me-2">
                            @else 
                            <img src="{{asset('assets/images/logo-b.png') }}" alt="" width="100" height="100" class="rounded-circle me-2">                 
                            @endif
                        </a>
                    </div>

                    @if (Session::has('status'))
                    <div class="alert alert-success alert-dismissible text-white" role="alert">
                        <span class="text-sm">{{ Session::get('status') }}</span>
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

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="form-group">    
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="email">
                                Email
                            </label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">    
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password">
                            Password
                            </label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" name="password" required autocomplete="new-password" autofocus>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">    
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password_confirmation">
                            Confirm Password
                            </label>
                            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" name="password_confirmation" required autocomplete="new-password" autofocus>
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="btn btn_theme">
                            {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>    
            </div> 
            <div class="col col-lg-8 col-12 d-flex align-items-center justify-content-center">
                <img src="{{asset('assets/admin/images/loginimg.png')}}" alt="Login">
            </div>
        </div>    
    </div> 
@endsection

@push ('after-scripts')

@endpush

