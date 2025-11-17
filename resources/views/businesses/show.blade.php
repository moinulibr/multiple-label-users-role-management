<x-admin-layout>
    
    <x-slot name="page_title">
        Business Details: {{ $business->name }}
    </x-slot>

    @push('css')
    <style>
        /* --- General Styles (আগের মতো) --- */
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        
        /* কন্টেইনারের মার্জিন এবং উইডথ ফিক্স করার জন্য এটি রিমুভ করা হলো:
           .cdbc-container { max-width: 1200px; margin: 0 auto; }
           এর বদলে, লেআউট স্লেটকে নির্ভর করা হবে। 
           যদি আপনার লেআউট স্লটে অতিরিক্ত প্যাডিং বা মার্জিন থাকে, এই স্টাইলটি সাহায্য করবে:
        */
        .content-area-fix {
            padding: 20px 0; /* Top/Bottom padding */
        }
        
        .cdbc-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        /* হেডার ফিক্স: এখানে মূল সমস্যা। আগের কোডে `page_title` স্লট ব্যবহার না করে হেডার নিজেই ডিজাইন করা হয়েছিল। */
        .cdbc-header-title {
            color: #7c3aed; 
            font-size: 24px;
            font-weight: 700;
            padding: 24px 30px;
            border-bottom: 1px solid #e5e7eb; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Custom View/Display Styles (আগের মতো) --- */
        .cdbc-body { padding: 30px; }
        
        .cdbc-section-title {
            font-weight: 700;
            font-size: 1.3rem;
            color: #f97316;
            margin-bottom: 25px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        .cdbc-section-title-alt {
            font-weight: 700;
            font-size: 1.3rem;
            color: #ec4899;
            margin-bottom: 25px;
            border-bottom: 2px solid #ec4899;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        .cdbc-info-item { margin-bottom: 15px; }
        .cdbc-info-label { font-weight: 600; color: #6b7280; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; display: block; }
        .cdbc-info-value { font-weight: 500; color: #1f2937; font-size: 1.1rem; }
        
        .badge-active { color: #155724; background-color: #d4edda; padding: 0.5em 0.75em; border-radius: 0.375rem; font-weight: 700; }
        .cdbc-separator-show { border-right: 1px solid #e5e7eb; }
        
        @media (max-width: 768px) {
            .cdbc-separator-show { border-right: none; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        }

        /* Button styles (আগের মতো) */
        .cdbc-btn-primary { background-color: #ff9933; border-color: #ff9933; color: #ffffff; font-weight: 700; padding: 10px 20px; border-radius: 8px; margin-right: 10px; }
        .cdbc-btn-primary:hover { background-color: #e68a00; border-color: #e68a00; }
        .cdbc-btn-secondary-red { background-color: #dc3545; border-color: #dc3545; color: #ffffff; font-weight: 700; padding: 10px 20px; border-radius: 8px; }
        .cdbc-btn-secondary-red:hover { background-color: #c82333; border-color: #c82333; }
    </style>
    @endpush

    {{-- Main Content ($slot) --}}
    
    {{-- 'container-fluid' ব্যবহার করা হলো, যা আপনার লেআউট সিস্টেমের সাথে ভালো কাজ করতে পারে --}}
    <div class="container-fluid content-area-fix"> 
        <div class="row">
            <div class="col-12">
                <div class="cdbc-card">
                    
                    {{-- Card Header: Title & Actions --}}
                    {{-- Title এখন Card এর ভেতরে ফিক্সড স্টাইলে দেখানো হচ্ছে --}}
                    <div class="cdbc-header-title">
                        <div>Hamza Tours update update</div>
                        <div class="d-flex">
                            {{-- Edit Button --}}
                            <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn cdbc-btn-primary">
                                <i class="mdi mdi-pencil"></i> EDIT BUSINESS
                            </a>
                            {{-- Back Button --}}
                            <a href="{{ route('admin.businesses.index') }}" class="btn cdbc-btn-secondary-red">
                                <i class="mdi mdi-arrow-left"></i> BACK TO LIST
                            </a>
                        </div>
                    </div>
                    
                    {{-- Card Body --}}
                    <div class="cdbc-body">

                        <div class="row">
                            
                            {{-- LEFT SIDE: Business Details --}}
                            <div class="col-md-7 cdbc-separator-show">
                                <h4 class="cdbc-section-title">General Information</h4>
                                
                                <div class="row">
                                    <div class="col-6 cdbc-info-item">
                                        <span class="cdbc-info-label">Official Email</span>
                                        <span class="cdbc-info-value">{{ $business->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6 cdbc-info-item">
                                        <span class="cdbc-info-label">Secondary Phone</span>
                                        <span class="cdbc-info-value">{{ $business->phone2 ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-6 cdbc-info-item">
                                        <span class="cdbc-info-label">Website</span>
                                        <span class="cdbc-info-value">
                                            @if($business->website)
                                                <a href="{{ $business->website }}" target="_blank">{{ $business->website }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-6 cdbc-info-item">
                                        <span class="cdbc-info-label">Status</span>
                                        <span class="cdbc-info-value">
                                            <span class="badge badge-active">{{ $business->status == 1 ? 'ACTIVE' : 'INACTIVE' }}</span>
                                        </span>
                                    </div>
                                    <div class="col-12 cdbc-info-item mt-3">
                                        <span class="cdbc-info-label">Full Address</span>
                                        <span class="cdbc-info-value">{{ $business->address ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- RIGHT SIDE: Assigned Owner Details --}}
                            <div class="col-md-5">
                                <h4 class="cdbc-section-title-alt">Assigned Owner Details</h4>
                                
                                @if($business->owner) 
                                    <div class="cdbc-info-item">
                                        <span class="cdbc-info-label">Owner Name (User Account)</span>
                                        <span class="cdbc-info-value font-weight-bold">{{ $business->owner->name }}</span>
                                    </div>
                                    <div class="cdbc-info-item">
                                        <span class="cdbc-info-label">Owner Phone (Login)</span>
                                        <span class="cdbc-info-value">{{ $business->owner->phone }}</span>
                                    </div>
                                    <div class="cdbc-info-item">
                                        <span class="cdbc-info-label">Owner Email</span>
                                        <span class="cdbc-info-value">{{ $business->owner->email ?? 'N/A' }}</span>
                                    </div>

                                    {{-- Owner Role (UserProfile থেকে) --}}
                                    @php
                                        $userProfile = $business->owner->profiles->where('business_id', $business->id)->first();
                                        $ownerRole = $userProfile && $userProfile->userType ? $userProfile->userType->name : 'N/A';
                                    @endphp

                                    <div class="cdbc-info-item">
                                        <span class="cdbc-info-label">Owner Role (for this Business)</span>
                                        <span class="cdbc-info-value text-primary font-weight-bold">{{ $ownerRole }}</span>
                                    </div>

                                    <div class="cdbc-info-item pt-2 border-top">
                                        <span class="cdbc-info-label">Can Manage Roles</span>
                                        <span class="cdbc-info-value">
                                            @if($business->can_manage_roles)
                                                <i class="mdi mdi-check-circle text-success"></i> Yes
                                            @else
                                                <i class="mdi mdi-close-circle text-danger"></i> No
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <p class="text-danger">Owner information is missing.</p>
                                @endif
                                
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-admin-layout>