<x-guest-layout>
    <x-slot name="title">Forgot Password | Admin</x-slot>

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

            <h4 class="text-dark mb-6 text-center">Forgot Your Password?</h4>
            <p class="text-center mb-4 text-muted">No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>

            <!-- Session Status (Success Message after sending email) -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
            

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="row">
                    <!-- Email Address -->
                    <div class="form-group col-md-12 mb-4">
                        <input type="email" class="form-control input-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required autofocus
                               placeholder="Email Address">
                    </div>
                    
                    <div class="col-md-12">
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-pill mb-4 mt-3">Email Password Reset Link</button>

                        <p>
                            <a class="text-blue" href="{{ route('login') }}">Back to Login</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
