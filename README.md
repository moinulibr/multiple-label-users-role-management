

````markdown
# 🧩 Custom User Role Management Setup Guide (Laravel)

এই গাইডটি অনুসরণ করে কোনো **third-party package** ব্যবহার না করে তৈরি করা **custom role এবং permission management module** আপনার Laravel প্রজেক্টে ইন্টিগ্রেট করুন।

---

## ⚙️ ১. ফাইল কপি (File Copying)

নিম্নলিখিত ফাইল ও ফোল্ডারগুলো তাদের নির্দিষ্ট পাথে কপি করুন:

| উৎস ফাইল/ফোল্ডার | গন্তব্য পাথ |
|------------------|-------------|
| `app/Models/Role.php` | `app/Models/Role.php` |
| `app/Traits/HasRolesAndPermissions.php` | `app/Traits/HasRolesAndPermissions.php` |
| `app/Http/Controllers/RoleController.php` | `app/Http/Controllers/RoleController.php` |
| `app/Http/Controllers/UserController.php` | `app/Http/Controllers/UserController.php` |
| `database/migrations/*_role_user_role_tables.php` | `database/migrations/` |
| `resources/views/admin/roles/` | `resources/views/admin/roles/` |
| `resources/views/admin/users/` | `resources/views/admin/users/` |

---

## 🧾 ২. কনফিগারেশন ফাইল তৈরি (Create `config/app_permissions.php`)

`config/app_permissions.php` নামে একটি নতুন ফাইল তৈরি করে নিচের কোডটি পেস্ট করুন:

```php
<?php

return [
    'permissions' => [
        // ড্যাশবোর্ড
        'dashboard.view',

        // ইউজার ম্যানেজমেন্ট
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'users.assign_role',

        // রোল ম্যানেজমেন্ট
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
        'roles.assign_permissions',
    ],
];
````

---

## 🧠 ৩. কোড ইন্টিগ্রেশন (Code Integration)

### A. `app/Models/User.php` এ Trait যোগ করুন

```php
use App\Traits\HasRolesAndPermissions; // <--- এই লাইনটি যোগ করুন

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions; // <--- এখানে Trait যোগ করুন
}
```

---

### B. `app/Providers/AuthServiceProvider.php` এ Blade Directives যোগ করুন

```php
use Illuminate\Support\Facades\Blade;

public function boot()
{
    $this->registerPolicies();

    // কাস্টম Blade Directives
    Blade::if('role', function ($role) {
        return auth()->check() && auth()->user()->hasRole($role);
    });

    Blade::if('can', function ($permission) {
        return auth()->check() && auth()->user()->can($permission);
    });
}
```

---

### C. `routes/web.php` এ রুট যোগ করুন

```php
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    
    // রোল ম্যানেজমেন্ট
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::post('roles/{role}/sync-permissions', [RoleController::class, 'syncPermissions'])
        ->name('roles.sync-permissions');

    // ইউজার ম্যানেজমেন্ট
    Route::resource('users', UserController::class);
});
```

---

### D. `resources/views/layouts/app.blade.php` এ AJAX ইন্টিগ্রেট করুন

**`<head>` ট্যাগের মধ্যে:**

```blade
@include('admin.roles.modal_styles')
```

**`</body>` ট্যাগের ঠিক আগে:**

```blade
@include('admin.roles.modal_scripts')

<script>
    // AJAX Content Area কে টার্গেট করে attachAllListeners ফাংশন কল করুন
    const mainContentArea = document.getElementById('main-ajax-content-area');
    
    // নেভিগেশন লিঙ্কগুলিতে content-load-link ক্লাস যুক্ত করা
    document.querySelectorAll('nav a').forEach(link => {
        if (link.href.includes('/admin/')) { 
            link.classList.add('content-load-link');
        }
    });
    
    if (typeof attachAllListeners === 'function' && mainContentArea) {
        attachAllListeners(mainContentArea);
    }
</script>
```

---

## 🧩 ৪. কমান্ড চালান (Run Commands)

সব ফাইল কপি ও কোড ইন্টিগ্রেট করার পর টার্মিনালে নিচের কমান্ডগুলো চালান:

```bash
# ডাটাবেস টেবিল তৈরি করুন
php artisan migrate

# কনফিগারেশন ক্যাশে পরিষ্কার করুন
php artisan config:clear
```

---

## ✅ কাজ শেষ!

এখন আপনার **Custom Role & Permission Management Module** সম্পূর্ণভাবে সেটআপ হয়ে গেছে এবং ব্যবহার উপযোগী। 🎉

---


### ✨ Developer Note

এই মডিউলটি Laravel-এর নেটিভ পদ্ধতি ব্যবহার করে তৈরি, যেখানে কোনো external package (যেমন Spatie Permission) ব্যবহার করা হয়নি।
এটি সম্পূর্ণভাবে lightweight, extendable, এবং production-ready।

```

---