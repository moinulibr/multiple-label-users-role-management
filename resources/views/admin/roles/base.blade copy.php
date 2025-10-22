{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management | @yield('title')</title>
    
    <style>
        /* BASE STYLES: Framework-Agnostic for Reusability */
        body { font-family: 'Arial', sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        .wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background-color: #212529; color: #fff; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .content { flex-grow: 1; padding: 30px; }
        .nav-link { display: block; padding: 10px 15px; color: #adb5bd; text-decoration: none; border-radius: 4px; margin-bottom: 8px; transition: background-color 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: #343a40; color: #fff; }
        .card { background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn { padding: 10px 18px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; transition: background-color 0.2s; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        
        /* Form & Table Styles */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #eee; padding: 12px; text-align: left; }
        .table th { background-color: #f1f1f1; font-weight: 600; text-transform: uppercase; font-size: 0.9em; }
        .tag { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; margin-right: 5px; background-color: #17a2b8; color: white; }
        
        /* Permissions Grid */
        .permission-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .module-card { border: 1px solid #e9ecef; padding: 15px; border-radius: 6px; background-color: #fff; }
        .module-title { font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #e9ecef; padding-bottom: 5px; color: #007bff; text-transform: capitalize; }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .wrapper { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 15px; }
            .permission-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <h1 style="font-size: 1.5em; color: #fff; margin-bottom: 25px;">Admin Dashboard</h1>
            <nav>
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <div style="height: 1px; background-color: #495057; margin: 15px 0;"></div>
                
                <a href="{{ route('admin.roles.index') }}" class="nav-link @if(request()->routeIs('admin.roles.*')) active @endif">Role & Permissions</a>
                <a href="{{ route('admin.users.index') }}" class="nav-link @if(request()->routeIs('admin.users.*')) active @endif">User Management</a>
            </nav>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif
            
            <h1 style="font-size: 2em; margin-bottom: 25px;">@yield('title')</h1>
            
            <div class="card">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management | @yield('title')</title>
    
    <style>
        /* BASE & LAYOUT STYLES */
        body { font-family: 'Arial', sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        .wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background-color: #212529; color: #fff; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .content { flex-grow: 1; padding: 30px; }
        .nav-link { display: block; padding: 10px 15px; color: #adb5bd; text-decoration: none; border-radius: 4px; margin-bottom: 8px; transition: background-color 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: #343a40; color: #fff; }
        .card { background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn { padding: 10px 18px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; transition: background-color 0.2s; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #eee; padding: 12px; text-align: left; }
        .permission-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 15px; }
        .module-card { border: 1px solid #e9ecef; padding: 15px; border-radius: 6px; background-color: #fff; }
        
        /* MODAL STYLES (Popup) */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.7); z-index: 1000;
            display: none; /* Hidden by default */
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: white; padding: 25px; border-radius: 8px;
            width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;
            position: relative;
            box-shadow: 0 5px 25px rgba(0,0,0,0.8);
            transform: scale(0.9); transition: transform 0.3s ease-out;
        }
        .modal-overlay.active .modal-content { transform: scale(1); }
        .modal-close { position: absolute; top: 10px; right: 20px; font-size: 24px; cursor: pointer; color: #aaa; text-decoration: none; }
        
        @media (max-width: 768px) {
            .wrapper { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .content { padding: 15px; }
            .permission-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <h1 style="font-size: 1.5em; color: #fff; margin-bottom: 25px;">Admin Panel</h1>
            <nav>
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <div style="height: 1px; background-color: #495057; margin: 15px 0;"></div>
                <a href="{{ route('admin.roles.index') }}" class="nav-link @if(request()->routeIs('admin.roles.*')) active @endif">Role & Permissions</a>
                <a href="{{ route('admin.users.index') }}" class="nav-link @if(request()->routeIs('admin.users.*')) active @endif">User Management</a>
            </nav>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif
            
            <h1 style="font-size: 2em; margin-bottom: 25px;">@yield('title')</h1>
            <div class="card">
                @yield('content')
            </div>
        </div>
    </div>
    
    <div id="ajax-modal-overlay" class="modal-overlay">
        <div class="modal-content">
            <a href="#" id="modal-close-btn" class="modal-close">&times;</a>
            <div id="modal-body-content">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalOverlay = document.getElementById('ajax-modal-overlay');
            const modalBody = document.getElementById('modal-body-content');
            const closeBtn = document.getElementById('modal-close-btn');
            
            const closeModal = () => {
                modalOverlay.classList.remove('active');
                modalOverlay.style.display = 'none';
                modalBody.innerHTML = '<p>Loading...</p>';
            };

            closeBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) { closeModal(); }
            });
            
            // 2. Open modal and fetch content function
            document.querySelectorAll('.open-modal-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    
                    modalOverlay.style.display = 'flex';
                    setTimeout(() => modalOverlay.classList.add('active'), 10);
                    
                    // Fetch content via AJAX
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        modalBody.innerHTML = html;
                        attachFormListener(); 
                    })
                    .catch(error => {
                        modalBody.innerHTML = `<p style="color:red;">Error loading content: ${error.message}</p>`;
                        console.error('Fetch error:', error);
                    });
                });
            });
            
            // 3. Handle Form Submission via AJAX (Redirect on success)
            function attachFormListener() {
                const form = modalBody.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(this);
                        const actionUrl = this.getAttribute('action');
                        
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.textContent;
                        submitBtn.textContent = 'Saving...';
                        submitBtn.disabled = true;

                        fetch(actionUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (response.status === 422) {
                                // Validation error response (assumes Laravel returns JSON errors)
                                return response.json().then(data => {
                                    alert('Validation failed! Please check the form.');
                                    console.error('Validation Errors:', data.errors);
                                    
                                    // Revert button state
                                    submitBtn.textContent = originalText;
                                    submitBtn.disabled = false;
                                    return Promise.reject('Validation failed');
                                });
                            }
                            // Assuming 200/201 status on success, which should return the redirect path
                            return response.json(); 
                        })
                        .then(data => {
                            if (data && data.success && data.redirect) {
                                window.location.href = data.redirect; // Redirect to index page
                            }
                        })
                        .catch(error => {
                            console.error('Submission error:', error);
                            // If the error wasn't validation, redirect anyway or show a generic error
                            if(error !== 'Validation failed') {
                                alert('An unexpected error occurred. Please refresh.');
                            }
                        });
                    });
                }
            }
        });
    </script>
</body>
</html>