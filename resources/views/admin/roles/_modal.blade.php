<div class="rms-modal-header">
    <h3>{{ $role->id ? 'Edit Role' : 'Create Role' }}</h3>
    <button onclick="rmsCloseModal()" style="background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
</div>

<form id="rms-form">
    @csrf
    @if($role->id)
        @method('PUT')
    @endif

    <div class="rms-form-group">
        <label>Display Name</label>
        <input type="text" name="display_name" value="{{ $role->display_name ?? '' }}" required class="rms-input">
    </div>

    <div class="rms-form-group">
        <label>Permissions</label>
        @foreach ($permissions as $module => $actions)
            <div class="rms-module">
                <strong>{{ ucfirst($module) }}</strong>
                @foreach ($actions as $perm)
                    <label>
                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                            {{ in_array($perm, $role->permissions ?? []) ? 'checked' : '' }}>
                        {{ $perm }}
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>

    <div style="text-align:right;margin-top:10px;">
        <button type="button" class="rms-btn" onclick="rmsCloseModal()">Cancel</button>
        <button type="submit" class="rms-btn rms-btn-primary">Save</button>
    </div>
</form>

<script>
document.getElementById('rms-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const url = '{{ $role->id ? route('admin.roles.update', $role) : route('admin.roles.store') }}';
    const method = '{{ $role->id ? 'PUT' : 'POST' }}';

    fetch(url, {
        method: method,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            rmsCloseModal();
            rmsReloadTable();
        }
    });
});
</script>

<style>
.rms-form-group { margin-bottom:10px; }
.rms-input { width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; }
.rms-module { border:1px solid #ddd; border-radius:6px; padding:10px; margin-bottom:8px; background:#fafafa; }
</style>
