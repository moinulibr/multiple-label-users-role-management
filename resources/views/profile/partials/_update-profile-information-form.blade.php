<!-- 1. Profile Information Update Form -->

<div class="card card-default mb-4" id="profile">
    <div class="card-header">
        <h2 class="mb-1">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">Update your account's profile information, email address, and profile picture.</p>
    </div>

    <div class="card-body">
        <!-- Form for updating Name, Email, and Picture -->
        <!-- *** ফাইল আপলোডের জন্য enctype="multipart/form-data" যোগ করা হয়েছে *** -->
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <!-- Profile Picture Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <label for="profile_picture" class="d-block mb-3 font-weight-bold">Profile Picture</label>

                    <div class="d-flex align-items-center">
                        <!-- Current Picture Display: $user->profile_picture ব্যবহার করা হয়েছে, যা Accessor-এর মাধ্যমে URL দেবে -->
                        <img src="{{ $user->profile_picture
                            ? $user->profile_picture
                            : asset('assets/admin/images/user/user-xs-01.jpg') }}" 
                             alt="Current Profile Picture" 
                             class="rounded-circle" 
                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #eee; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                        
                        <div class="ml-4">
                            <!-- File Input for New Picture -->
                            <div class="form-group mb-2">
                                <label for="profile_picture">Upload new picture</label>
                                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                                @error('profile_picture')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Remove Picture Option (Conditional) -->
                            {{-- @if ($user->profile_picture)
                            <div class="form-check">
                                <!-- চেকবক্সের নাম: 'remove_profile_picture' -->
                                <input class="form-check-input" type="checkbox" name="remove_profile_picture" value="1" id="removePictureCheck">
                                <label class="form-check-label text-danger" for="removePictureCheck">
                                    Remove Current Picture
                                </label>
                            </div>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="mt-0 mb-4">

            <!-- Name and Email Inputs -->
            <div class="row mb-2">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        {{-- $user ভেরিয়েবলটি মূল ফাইল থেকে আসছে --}}
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        <!-- Laravel Validation Error Display -->
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- Email Verification Check -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm mt-2 text-warning">Your email address is unverified. <a href="#" onclick="event.preventDefault(); document.getElementById('send-verification').submit();" class="text-primary">Click here to re-send the verification email.</a></p>
            @endif
            <!-- End Email Verification Check -->

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-pill">Save Changes</button>
                <!-- Status message (for Alpine.js) -->
                @if(session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success ml-3 align-self-center">Saved.</p>
                @endif
            </div>
        </form>
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" style="display: none;">
            @csrf
        </form>
    </div>

</div>
<!-- End Profile Information Update Form -->
