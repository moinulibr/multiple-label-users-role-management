{{--
|--------------------------------------------------------------------------
| Role Form Content (Loaded into Modal)
|--------------------------------------------------------------------------
| Contains form fields and the permissions grid.
| Expects $role and $permissions variables.
--}}

<form id="roleForm"
action="{{ $role->exists ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}"
method="POST"
data-method="{{ $role->exists ? 'PUT' : 'POST' }}">
@csrf

<h4 style="color: #007bff; margin-bottom: 15px;">{{ $role->exists ? 'রোল এডিট করুন' : 'নতুন রোল তৈরি করুন' }}</h4>

<div class="form-row">
    <div class="form-col">
        <label for="display_name">ডিসপ্লে নাম <span style="color: #dc3545;">*</span></label>
        <input type="text" 
               class="form-control" 
               id="display_name" 
               name="display_name" 
               value="{{ old('display_name', $role->display_name ?? '') }}" 
               required
               {{ $role->name === 'super_admin' ? 'disabled' : '' }}>
        <small class="text-danger" id="error-display_name"></small>
    </div>
    <div class="form-col">
        <label for="name">সিস্টেমের নাম (ঐচ্ছিক)</label>
        <input type="text" 
               class="form-control" 
               id="name" 
               name="name" 
               value="{{ old('name', $role->name ?? '') }}"
               {{ $role->name === 'super_admin' ? 'disabled' : '' }}>
        <small class="text-danger" id="error-name"></small>
    </div>
</div>

<hr style="border-top: 1px solid #ccc; margin: 20px 0;">

<h4 style="color: #007bff; margin-bottom: 15px;">পারমিশন নির্ধারণ</h4>

<div class="alert-info">
    <i class="fas fa-info-circle"></i> এই রোলের জন্য পারমিশন দিতে চেকবক্সগুলো চেক করুন।
</div>

{{-- Permissions Checkboxes Grid --}}
@if (!empty($permissions))
    <div class="permission-grid">
        @foreach($permissions as $moduleName => $actions)
            @php
                // ডিসপ্লে নাম তৈরি
                $moduleDisplayName = ucwords(str_replace('_', ' ', $moduleName));
                // Check if all permissions are checked
                $allChecked = true;
                foreach ($actions as $action) {
                    $permissionKey = $moduleName . '.' . $action;
                    if (!$role->hasPermissionTo($permissionKey)) {
                        $allChecked = false;
                        break;
                    }
                }
            @endphp
            
            <div class="permission-module">
                <div class="module-header">
                    <input type="checkbox" id="module-{{ $moduleName }}" class="form-check-input check-all" data-module="{{ $moduleName }}" {{ $allChecked ? 'checked' : '' }} {{ $role->name === 'super_admin' ? 'disabled' : '' }}> 
                    <label for="module-{{ $moduleName }}" style="margin: 0; font-weight: normal; font-size: 14px; margin-left: 5px;">{{ $moduleDisplayName }} (সব সিলেক্ট)</label>
                </div>
                <div class="module-body">
                    @foreach($actions as $action)
                        @php
                            $permissionKey = $moduleName . '.' . $action;
                            $actionDisplayName = ucwords(str_replace('_', ' ', $action));
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input permission-checkbox" 
                                   type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $permissionKey }}" 
                                   id="perm-{{ $permissionKey }}"
                                   data-module="{{ $moduleName }}"
                                   {{ $role->hasPermissionTo($permissionKey) ? 'checked' : '' }}
                                   {{ $role->name === 'super_admin' ? 'disabled' : '' }}>
                            <label style="margin: 0; font-weight: normal; font-size: 14px;" for="perm-{{ $permissionKey }}">
                                {{ $actionDisplayName }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@else
    <div style="padding: 10px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px;">পারমিশন কনফিগারেশন পাওয়া যায়নি।</div>
@endif

<div class="modal-footer-custom">
    <button type="button" class="btn" style="background-color: #6c757d; color: white;" onclick="document.getElementById('roleModal').style.display='none'">বন্ধ করুন</button>
    {{-- Super Admin role cannot be updated --}}
    @if ($role->name !== 'super_admin')
        <button type="submit" class="btn btn-success" style="background-color: #28a745; color: white;">রোল সেভ করুন</button>
    @else
        <button type="button" class="btn btn-disabled" disabled>সুপার অ্যাডমিন লকড</button>
    @endif
</div>


</form>

<script>
// এই স্ক্রিপ্টটি শুধু Permissions Checkbox এর 'Select All' লজিক পরিচালনা করার জন্য।
$(document).ready(function() {
// 'Select All' চেকবক্সের জন্য লজিক
$('.check-all').on('change', function() {
const module = $(this).data('module');
const isChecked = $(this).is(':checked');

        // একই মডিউলের সব চেকবক্স আপডেট করা
        $(&#39;.permission-checkbox[data-module=&quot;&#39; + module + &#39;&quot;]&#39;).prop(&#39;checked&#39;, isChecked);
    });

    // ইন্ডিভিজুয়াল চেকবক্স আপডেট হলে &#39;Select All&#39; কে আপডেট করা
    $(&#39;.permission-checkbox&#39;).on(&#39;change&#39;, function() {
        const module = $(this).data(&#39;module&#39;);
        const totalCheckboxes = $(&#39;.permission-checkbox[data-module=&quot;&#39; + module + &#39;&quot;]&#39;).length;
        const checkedCheckboxes = $(&#39;.permission-checkbox[data-module=&quot;&#39; + module + &#39;&quot;]:checked&#39;).length;
        
        // যদি সব চেক করা থাকে, তাহলে &#39;Select All&#39; চেক হবে, অন্যথায় আনচেক
        $(&#39;#module-&#39; + module).prop(&#39;checked&#39;, totalCheckboxes === checkedCheckboxes);
    });
});


</script>