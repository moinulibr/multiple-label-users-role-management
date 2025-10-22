<div class="role-list-wrapper">
@if($roles->isEmpty())
<div class="empty-state">কোনো রোল পাওয়া যায়নি। এখনই একটি তৈরি করুন!</div>
@else
<table class="roles-table">
<thead>
<tr>
<th>ID</th>
<th>ডিসপ্লে নাম</th>
<th>সিস্টেম নাম (Slug)</th>
<th>অ্যাকশন</th>
</tr>
</thead>
<tbody>
@foreach ($roles as $role)
<tr>
<td>{{ $role->id }}</td>
<td>{{ $role->display_name }}</td>
<td>{{ $role->name }}</td>
<td class="action-cell">
<!-- Edit Link (Modal ওপেন করার জন্য) -->
<a href="{{ route('admin.roles.edit', $role) }}" class="action-link edit-link open-modal-link">অনুমতি এডিট করুন</a>

                        @if ($role->name !== 'super_admin')
                            <!-- Delete Form -->
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="delete-form" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link delete-link" style="background:none;">মুছে ফেলুন</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif


</div>