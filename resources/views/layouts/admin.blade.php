<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.roles.modal_styles')

    <style>
        /* BASE STYLES for the content area */
        .card { background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;}
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 18px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; transition: background-color 0.2s; }
        .btn-primary { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="content">
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
        
        <div id="main-ajax-content-area">
            @yield('content')
        </div>
        
    </div>

    @include('admin.roles.modal_scripts')
    
    <script>
        // Ensure sidebar links use the content-load-link class for AJAX navigation
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.classList.add('content-load-link');
        });
    </script>
</body>
</html>