<x-guest-layout>
    <x-slot name="title">Login | Admin</x-slot>

    <div class="card card-default mb-0">
        <div class="card-header pb-0">
            <div class="app-brand w-100 d-flex justify-content-center border-bottom-0">
                <a class="w-auto pl-0" href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Mono Logo">
                    <span class="brand-name text-dark">MONO</span>
                </a>
            </div>
        </div>
        <div class="card-body px-5 pb-5 pt-0">

            <h4 class="text-dark mb-6 text-center">Sign in to your Account</h4>

            <!-- Session Status (Success Messages, e.g., Password Reset) -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Validation Errors (General Login/Credentials Error) -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf 
                <div class="row">
                    <!-- Email Input -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required autofocus
                               placeholder="Email">
                        <!-- Individual Email Error (if needed, though general error is often used for login) -->
                    </div>

                    <!-- Password Input -->
                    <div class="form-group col-md-12 ">
                        <input type="password" class="form-control input-lg @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="current-password"
                               placeholder="Password">
                    </div>
                    
                    <div class="col-md-12">

                        <div class="d-flex justify-content-between mb-3">

                            <!-- Remember Me Checkbox -->
                            <div class="custom-control custom-checkbox mr-3 mb-3">
                                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                <label class="custom-control-label" for="remember_me">Remember me</label>
                            </div>

                            <!-- Forgot Password Link -->
                            @if (Route::has('password.request'))
                                <a class="text-color" href="{{ route('password.request') }}"> Forgot password? </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-pill mb-4">Sign In</button>

                        <p>Don't have an account yet?
                            <a class="text-blue" href="{{ route('register') }}">Sign Up</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
