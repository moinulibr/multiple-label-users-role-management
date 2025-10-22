<x-guest-layout>
    <x-slot name="title">Sign Up | Admin</x-slot>

    <div class="card card-default mb-0">
        <div class="card-header pb-0">
            <div class="app-brand w-100 d-flex justify-content-center border-bottom-0">
                <a class="w-auto pl-0" href="{{ route('dashboard') }}">
                    <!-- Asset Path for Logo -->
                    <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Mono Logo">
                    <span class="brand-name text-dark">MONO</span>
                </a>
            </div>
        </div>
        <div class="card-body px-5 pb-5 pt-0">
            <h4 class="text-dark mb-6 text-center">Create New Account</h4>

            <!-- Validation Errors -->
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

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    
                    <!-- Name Input Field -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="text" class="form-control input-lg @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required autofocus
                               autocomplete="name"
                               placeholder="Full Name">
                        <!-- Removed individual error message display as general $errors->any() block handles it -->
                    </div>

                    <!-- Email Input Field -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required
                               autocomplete="username"
                               placeholder="Email">
                    </div>

                    <!-- Password Input Field -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="password" class="form-control input-lg @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="new-password"
                               placeholder="Password">
                    </div>
                    
                    <!-- Confirm Password Input Field -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="password" class="form-control input-lg @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               autocomplete="new-password"
                               placeholder="Confirm Password">
                    </div>
                    
                    <div class="col-md-12">
                        <!-- Register Button -->
                        <button type="submit" class="btn btn-primary btn-pill mb-4 mt-3">Register</button>

                        <p>Already have an account?
                            <a class="text-blue" href="{{ route('login') }}">Log In</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
