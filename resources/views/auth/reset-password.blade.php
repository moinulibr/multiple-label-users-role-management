<x-guest-layout>
    <x-slot name="title">Reset Password | Admin</x-slot>

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

            <h4 class="text-dark mb-6 text-center">পাসওয়ার্ড রিসেট</h4>
            <p class="text-center mb-4">আপনার অ্যাকাউন্ট ইমেইল দিন। আমরা আপনাকে পাসওয়ার্ড রিসেট লিংক পাঠাবো।</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="row">
                    <div class="form-group col-md-12 mb-4">
                        <input type="email" class="form-control input-lg" id="email" name="email" required autofocus
                            placeholder="ইমেইল">
                    </div>
                    
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-pill mb-4 mt-3">লিংক পাঠান</button>

                        <p>
                            <a class="text-blue" href="{{ route('login') }}">লগইন পেজে ফিরে যান</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
