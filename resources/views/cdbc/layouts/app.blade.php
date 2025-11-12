<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin') - cdbc</title>
  <link rel="stylesheet" href="{{ asset('css/cdbc-admin.css') }}">
  <script src="{{ asset('js/cdbc-admin.js') }}" defer></script>
</head>
<body class="cdbc-body">
  <div class="cdbc-topbar">
    <div class="cdbc-topbar-inner">
      <a href="{{ route('admin.roles.index') }}" class="cdbc-brand">cdbc Admin</a>
      <nav class="cdbc-topnav">
        <a href="{{ route('admin.users.index') }}">Users</a>
        <a href="{{ route('admin.roles.index') }}">Roles</a>
      </nav>
    </div>
  </div>

  <main class="cdbc-main">
    <div class="cdbc-container">
      @if(session('success'))
        <div class="cdbc-alert cdbc-alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="cdbc-alert cdbc-alert-error">{{ session('error') }}</div>
      @endif

      @yield('content')
    </div>
  </main>

  <footer class="cdbc-footer">© {{ date('Y') }} cdbc</footer>
</body>
</html>
