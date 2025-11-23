<x-admin-layout>
  
    {{-- page title (header name Named Slot) --}}
    <x-slot name="page_title">
        Blank Page - page title
    </x-slot>



    {{-- main content ($slot) --}}

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h2>এখানে পেজের মূল তথ্য শুরু হবে  </h2>
                </div>
                <div class="card-body">
                    <!-- আপনার ফর্ম বা ডেটা টেবিল এর কোড এখানে দিন -->
                    <p>এই ব্ল্যাংক পেজটি কপি করে আপনার কাজের কোড বসিয়ে দিন।</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- মূল কন্টেন্ট ($slot) --}}

    
    @push('css')
        <!-- Primary Theme CSS (Dashboard-এর বিশেষ স্টাইল) -->
    @endpush
    
    @push('script')
        <!-- Core & Utility JS -->

        <!-- Charting Library (in code - spline-area, mixed-chart থাকার জন্য এটি জরুরি) -->
        {{-- <script src="{{asset('assets/admin/plugins/apexcharts/apexcharts.min.js')}}"></script>

        <!-- Template Specific JS (যেমন অফক্যানভাস, ড্রপডাউন এবং অন্যান্য UI ফাংশনালিটির জন্য) -->
        <script src="{{ asset('assets/admin/js/chart.js') }}"></script> --}}
    @endpush
    
</x-admin-layout>




{{-- অ্যাসেট পাথ (Laravel Format)	কোন পেজে বসবে / নোট
{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/simplebar/simplebar.min.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
https://unpkg.com/hotkeys-js/dist/hotkeys.min.js	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/toaster/toastr.min.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/js/mono.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/js/custom.js') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/apexcharts/apexcharts.js') }}	চার্ট বা গ্রাফ দেখানোর জন্য (ড্যাশবোর্ড পেজ)।
{{ asset('assets/admin/js/chart.js') }}	কাস্টম চার্ট লজিক বা ইনিশিয়ালাইজেশনের জন্য (ড্যাশবোর্ড পেজ)।
{{ asset('assets/admin/plugins/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}	ডেটা টেবিল প্রদর্শনকারী পেজ।
{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js') }}	ভেক্টর ম্যাপ ব্যবহারের জন্য।
{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-world-mill.js') }}	বিশ্ব ম্যাপ ডেটার জন্য।
{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-us-aea.js') }}	মার্কিন ম্যাপ ডেটার জন্য।
{{ asset('assets/admin/js/map.js') }}	ম্যাপের কাস্টম লজিক বা ইনিশিয়ালাইজেশনের জন্য।
{{ asset('assets/admin/plugins/daterangepicker/moment.min.js') }}	ডেট রেঞ্জ পিকার ব্যবহারের জন্য অপরিহার্য।
{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}	ডেট রেঞ্জ পিকার ব্যবহারের জন্য।
https://cdn.quilljs.com/1.3.6/quill.js	রিচ টেক্সট এডিটর পেজের জন্য (Quill CDN)। --}}


{{-- অ্যাসেট পাথ (Laravel Format)	কোন পেজে বসবে / নোট
`https://fonts.googleapis.com/css?family=Karla:400,700	Roboto`
{{ asset('assets/admin/plugins/material/css/materialdesignicons.min.css') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/simplebar/simplebar.css') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/nprogress/nprogress.css') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/toaster/toastr.min.css') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/css/style.css') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/images/favicon.png') }}	কোর অ্যাসেট (মাস্টার লেআউট Admin-layout-এ থাকবে)।
{{ asset('assets/admin/plugins/DataTables/DataTables-1.10.18/css/jquery.dataTables.min.css') }}	ডেটা টেবিল পেজ।
{{ asset('assets/admin/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}	ম্যাপ ডিসপ্লে পেজ।
{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}	তারিখ নির্বাচনের পেজ।
https://cdn.quilljs.com/1.3.6/quill.snow.css	রিচ টেক্সট এডিটর পেজ (Quill CDN)। --}}