<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel Admin') }}</title>

    <!-- Tailwind CSS লোড করা হয়েছে, আপনি আপনার CSS লিঙ্ক করতে পারেন -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* আপনার কাস্টম স্টাইল এখানে যোগ করুন */
        [x-cloak] { display: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: true }">

    <!-- 1. সাইডবার (Named Slot) -->
    <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto bg-gray-800 p-4 transform md:relative md:translate-x-0">
        {{ $sidebar }} <!-- Named Slot: $sidebar -->
    </aside>

    <div class="md:ml-64 transition-all duration-300 min-h-screen">
        
        <!-- 2. নেভিগেশন/হেডার (Named Slot) -->
        <header class="bg-white shadow-md p-4 flex items-center justify-between">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="font-semibold text-lg">{{ $header ?? 'ড্যাশবোর্ড' }}</div>
            <div class="flex items-center space-x-4">
                {{ $navbar_items ?? '' }}
                <a href="#" class="text-sm text-gray-500 hover:text-gray-900">লগআউট</a>
            </div>
        </header>

        <!-- 3. মূল কন্টেন্ট (Default Slot) -->
        <main class="p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">{{ $page_title ?? '' }}</h1>
            {{ $slot }} <!-- Default Slot: পেজের সমস্ত মূল কন্টেন্ট এখানে রেন্ডার হবে -->
        </main>
    </div>
    
    <script src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
</body>
</html>