<div>
    <button class="rms-close-btn" onclick="rmsCloseModal()">X</button>
    <h3>{{ $role->exists ? 'Edit Role' : 'Create Role' }}</h3>

    <form id="rms-role-form">
        @csrf
        @if($role->exists)
            @method('PUT')
        @endif

        <label>Display Name</label>
        <input type="text" name="display_name" value="{{ $role->display_name ?? '' }}" required style="width:100%;margin-bottom:10px;padding:6px">

        <h4>Permissions</h4>
        @foreach($modules as $module => $perms)
            <div style="margin-bottom:10px;border:1px solid #ddd;padding:10px;border-radius:6px">
                <strong>{{ $module }}</strong><br>
                @foreach($perms as $perm)
                    <label style="margin-right:10px">
                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                            {{ in_array($perm, $role->permissions ?? []) ? 'checked' : '' }}>
                        {{ ucfirst($perm) }}
                    </label>
                @endforeach
            </div>
        @endforeach

        <button type="submit" class="rms-btn rms-btn-primary">Save</button>
    </form>
</div>

<script>
document.getElementById('rms-role-form').onsubmit = e => {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    const method = '{{ $role->exists ? 'PUT' : 'POST' }}';
    const url = '{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}';

    fetch(url, {method, body:data, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                rmsCloseModal();
                rmsReloadTable();
            }
        });
};
</script>
