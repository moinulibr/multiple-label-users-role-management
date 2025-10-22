<table class="rms-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Display Name</th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->display_name }}</td>
                <td>{{ implode(', ', $role->permissions ?? []) }}</td>
                <td>
                    <button class="rms-btn rms-btn-edit" onclick="rmsEditRole({{ $role->id }})">Edit</button>
                    <button class="rms-btn rms-btn-delete" onclick="rmsDeleteRole({{ $role->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
