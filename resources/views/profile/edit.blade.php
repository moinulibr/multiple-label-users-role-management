<x-admin-layout>

<x-slot name="page_title">
    Profile Settings
</x-slot>

@php 
// Auth::user() দিয়ে বর্তমানে লগইন করা ইউজারকে আনা হচ্ছে 
$user = Auth::user();

// Active মেনু নির্ধারণ করা হচ্ছে
$active_setting = 'account'; 
@endphp

<div class="content-wrapper">
    <div class="content">

        <!-- প্রোফাইল হেডার কার্ড অন্তর্ভুক্ত করা হলো -->
        @include('profile.partials._profile-header')

        <div class="row">
            <!-- বাম দিকের সেটিংস নেভিগেশন অন্তর্ভুক্ত করা হলো (col-xl-3) -->
            <div class="col-xl-3">
                @include('profile.partials._settings-nav')
            </div>
            
            <!-- ডান দিকের সেটিংস ফর্ম/কন্টেন্ট অন্তর্ভুক্ত করা হলো (col-xl-9) -->
            <div class="col-xl-9">
                
                {{-- নিম্নলিখিত ফাইলগুলো এখন একটি অনুমান করা 'partials' ফোল্ডার থেকে ইনক্লুড করা হচ্ছে --}}

                <!-- 1. Profile Information Update Form -->
                @include('profile.partials._update-profile-information-form')

                <!-- 2. Password Update Form -->
                @include('profile.partials._update-password-form')

                <!-- 3. Delete Account Section -->
                @include('profile.partials._delete-user-form')

            </div>
        </div>

    </div>
</div>

@push('css')
    <!-- Primary Theme CSS -->
@endpush

@push('script')
    <!-- Core & Utility JS -->
@endpush

</x-admin-layout>