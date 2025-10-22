<x-admin-layout>
    {{-- 1. $slot-এ ডাটা পাস করার জন্য <x-slot> ব্যবহার --}}
    
    {{-- পেজের টাইটেল (header নামে Named Slot) --}}
    <x-slot name="page_title">
        আপনার অ্যাডমিন ড্যাশবোর্ড
    </x-slot>

    {{-- সাইডবারের কন্টেন্ট (sidebar নামে Named Slot) --}}
    <x-slot name="sidebar">
        <h2 class="text-white text-xl font-bold mb-6">Laragon Admin</h2>
        <nav class="space-y-2">
            <a href="#" class="block px-3 py-2 rounded-md text-white bg-gray-900">ড্যাশবোর্ড</a>
            <a href="{{-- {{ route('trucks.index') }} --}}" class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-700">ট্রাক্স ম্যানেজ করুন</a>
            <a href="#" class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-700">সেটিংস</a>
        </nav>
    </x-slot>

    {{-- মূল কন্টেন্ট ($slot) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-2">মোট ট্রাক</h3>
            <p class="text-3xl text-blue-600">42</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-2">অপেক্ষমাণ এন্ট্রি</h3>
            <p class="text-3xl text-yellow-600">12</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-2">ইউজার</h3>
            <p class="text-3xl text-green-600">8</p>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">স্বাগতম!</h2>
        <p class="text-gray-600">আপনার অ্যাডমিন প্যানেলটি Blade Components এর মাধ্যমে সফলভাবে সেটআপ করা হয়েছে।</p>
    </div>
</x-admin-layout>