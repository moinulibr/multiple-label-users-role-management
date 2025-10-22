<!-- 3. Delete Account Section -->

<div class="card card-default mb-4" id="delete-account">
<div class="card-header">
<h2 class="mb-1 text-danger">Delete Account</h2>
<p class="mt-1 text-sm text-gray-600">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
</div>

<div class="card-body">
    
    {{-- এখানে @can('manage-users') ব্যবহার করা হয়েছে, ধরে নেওয়া হচ্ছে $user মডেল-এ can() মেথড আছে। --}}
    @can('manage-users')
        <p class="text-warning">As an administrator, you cannot delete your own account from this self-service profile page. Admin accounts can only be deleted via the User Management panel by a Super Admin.</p>
    @else
        <button 
            type="button" 
            class="btn btn-danger btn-pill" 
            x-data="" 
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            Delete Account
        </button>

        <!-- Delete Account Modal Logic -->
        {{-- এই মোডালটি Alpine.js বা অন্য কোনো ফ্রেমওয়ার্ক দ্বারা প্রদর্শিত হবে, আমরা শুধু স্ট্রাকচারটি ঠিক করে দিচ্ছি --}}
        <div x-data="{ show: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }" x-show="show" x-transition style="display: none;">
            <div class="modal fade show d-block" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form method="post" action="{{ route('profile.destroy') }}" class="modal-content p-4">
                        @csrf
                        @method('delete')
                    
                        <h4 class="modal-title mb-3">Are you sure you want to delete your account?</h4>
                        <p class="text-sm text-gray-600">
                            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.
                        </p>
                    
                        <div class="mt-4">
                            <label for="delete-password" class="sr-only">Password</label>
                            <input
                                id="delete-password"
                                name="password"
                                type="password"
                                class="form-control"
                                placeholder="Password"
                                x-ref="passwordInput"
                            />
                            @error('password', 'userDeletion')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary mr-2" x-on:click="show = false">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Delete Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Backdrop -->
            <div class="modal-backdrop fade show"></div>
        </div>
        <!-- End Delete Account Modal Logic -->
    @endcan

</div>

</div>
<!-- End Delete Account Section -->