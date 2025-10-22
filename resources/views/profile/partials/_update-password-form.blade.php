<!-- 2. Password Update Form -->

<div class="card card-default mb-4" id="password">
<div class="card-header">
<h2 class="mb-1">Update Password</h2>
<p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
</div>

<div class="card-body">
    <!-- Form for updating Password (uses route('password.update')) -->
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password field is crucial for security -->
        <div class="form-group mb-4">
            <label for="current_password">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="password">New password</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
            @error('password', 'updatePassword')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <small class="text-danger d-block mt-2">{{ $message }}</small>
            @enderror
        </div>

        <div class="d-flex justify-content-end mt-6">
            <button type="submit" class="btn btn-primary mb-2 btn-pill">Update Password</button>
            @if(session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success ml-3 align-self-center">Saved.</p>
            @endif
        </div>
    </form>
</div>

</div>
<!-- End Password Update Form -->