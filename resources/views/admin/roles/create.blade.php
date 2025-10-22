{{-- <style>
    .permission-module {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .action-checkbox {
        margin-right: 15px;
        display: inline-block;
    }
    .module-title {
        font-weight: bold;
        color: #333;
        text-transform: capitalize;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }
</style>

<h2>নতুন Role তৈরি করুন এবং পারমিশন দিন</h2>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    
    <div>
        <label for="display_name">Role নাম:</label>
        <input type="text" id="display_name" name="display_name" required>
    </div>
    <br>
    
    <h3>পারমিশন অ্যাসাইন করুন</h3>

    @php
        // বর্তমানে assign করা পারমিশন (যদি edit মোড হয়)
        $rolePermissions = []; // Role তৈরি করার সময় এটি খালি থাকবে
    @endphp

    @foreach ($permissions as $module => $actions)
        <div class="permission-module">
            <div class="module-title">{{ str_replace('_', ' ', $module) }}</div>

            @foreach ($actions as $action)
                @php
                    $permissionName = "{$module}.{$action}";
                    $isChecked = in_array($permissionName, $rolePermissions);
                @endphp
                
                <div class="action-checkbox">
                    <input 
                        type="checkbox" 
                        name="permissions[]" 
                        value="{{ $permissionName }}"
                        id="{{ $permissionName }}"
                        {{ $isChecked ? 'checked' : '' }}
                    >
                    <label for="{{ $permissionName }}">
                        {{ str_replace('_', ' ', $action) }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach

    <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">
        Role তৈরি করুন ও সেভ করুন
    </button>
</form> --}}


{{-- <x-admin-layout>
  
    <x-slot name="page_title">
        Role Crate
    </x-slot>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রিমিয়াম রোল ও পারমিশন ম্যানেজমেন্ট</title>
    <!-- Inter Font Load -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS using 'curpm-' prefix for guaranteed isolation -->
    <style>
        /* Base Styles */
        .curpm-body {
            font-family: 'Inter', sans-serif;
            background-color: #eef1f7; /* Very light, cool background */
            color: #2c3e50;
            padding: 40px 20px;
        }

        /* Container and Structure */
        .curpm-container {
            max-width: 1280px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .curpm-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #3159A3; /* Deep Blue Primary */
            margin-bottom: 5px;
        }
        
        .curpm-header p {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .curpm-grid {
            display: grid;
            grid-template-columns: 350px 1fr; /* Fixed width for details, flexible for permissions */
            gap: 40px;
        }

        /* Form Elements */
        .curpm-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3159A3;
            font-size: 0.95rem;
        }

        .curpm-input, .curpm-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fcfcfc;
            margin-bottom: 20px;
        }

        .curpm-input:focus, .curpm-textarea:focus {
            border-color: #3159A3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 89, 163, 0.15);
        }

        /* Submit Button */
        .curpm-btn {
            background: linear-gradient(135deg, #3159A3 0%, #4a77cc 100%);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 15px rgba(49, 89, 163, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .curpm-btn:hover {
            box-shadow: 0 5px 10px rgba(49, 89, 163, 0.6);
            transform: translateY(-2px);
        }
        
        .curpm-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(49, 89, 163, 0.4);
        }

        /* Permission Panel */
        .curpm-permission-panel {
            border-left: 1px solid #e0e0e0;
            padding-left: 40px;
        }
        
        .curpm-permission-panel h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 25px;
        }

        /* Accordion Items */
        .curpm-accordion-item {
            background-color: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        
        .curpm-accordion-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .curpm-accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .curpm-accordion-header:hover {
            background-color: #f5f8ff; /* Lightest blue on hover */
        }

        .curpm-module-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c3e50;
            flex-grow: 1;
        }

        /* Custom Toggle All Link */
        .curpm-toggle-all {
            font-size: 0.9rem;
            font-weight: 600;
            color: #3159A3;
            cursor: pointer;
            margin-right: 20px;
            text-transform: uppercase;
            /* Ensures toggle button click doesn't bubble up to close the accordion */
            pointer-events: all;
        }

        .curpm-accordion-icon {
            font-size: 24px;
            transition: transform 0.3s;
            color: #3159A3;
        }

        .curpm-accordion-header.active .curpm-accordion-icon {
            transform: rotate(180deg);
        }

        .curpm-accordion-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.55, 0, 0.1, 1), padding 0.4s ease-in-out;
            background-color: #f9f9f9;
        }
        
        .curpm-accordion-content.open {
            max-height: 800px; /* Safe height for content */
            padding: 20px;
            border-top: 1px solid #eee;
        }

        /* Permission Checkboxes - Custom Styling */
        .curpm-permission-group {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .curpm-checkbox-wrapper {
            display: flex;
            align-items: center;
            width: 180px;
        }

        .curpm-checkbox {
            /* Hide the default checkbox */
            opacity: 0;
            position: absolute;
        }

        .curpm-permission-title {
            position: relative;
            padding-left: 28px; /* Space for custom box */
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        /* Custom Checkbox Appearance */
        .curpm-permission-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border: 2px solid #a0aec0;
            border-radius: 4px;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .curpm-checkbox:checked + .curpm-permission-title::before {
            background-color: #3159A3;
            border-color: #3159A3;
        }

        .curpm-checkbox:checked + .curpm-permission-title::after {
            content: '\2713'; /* Checkmark symbol */
            position: absolute;
            top: 50%;
            left: 3px;
            transform: translateY(-50%);
            color: white;
            font-size: 14px;
            font-weight: 700;
        }

        /* Custom Message Box (Alert Replacement) */
        .curpm-message-box {
            position: fixed;
            top: 20px;
            right: 20px;
            display: none; /* Hidden by default */
            align-items: flex-start;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .curpm-message-box.success {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            color: #155724;
        }
        .curpm-message-box.error {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        .curpm-message-box.info {
            background-color: #cce5ff;
            border-left: 5px solid #007bff;
            color: #004085;
        }

        .curpm-message-box strong {
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        .curpm-message-box ul {
            list-style: disc;
            margin-left: 20px;
            padding: 0;
            font-size: 0.9rem;
        }
        .curpm-message-box p {
            margin: 5px 0;
            font-size: 0.95rem;
        }

        /* Responsiveness */
        @media (max-width: 1000px) {
            .curpm-grid {
                grid-template-columns: 1fr;
            }
            .curpm-permission-panel {
                border-left: none;
                padding-left: 0;
                padding-top: 30px;
                margin-top: 30px;
                border-top: 1px solid #e0e0e0;
            }
        }
        
        @media (max-width: 600px) {
            .curpm-container {
                padding: 20px;
            }
            .curpm-permission-group {
                flex-direction: column;
                gap: 15px;
            }
            .curpm-checkbox-wrapper {
                width: 100%;
            }
            .curpm-header h1 {
                font-size: 1.6rem;
            }
            .curpm-permission-panel h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="curpm-body">

    <!-- Custom Message Box for Alert Replacement -->
    <div id="curpm-message-box" class="curpm-message-box">
        <div>
            <strong id="curpm-message-title"></strong>
            <div id="curpm-message-body"></div>
        </div>
    </div>
    <!-- End Custom Message Box -->

    <div class="curpm-container">
        <header class="curpm-header">
            <h1 id="curpm-page-title">নতুন রোল তৈরি করুন</h1>
            <p>এই রোলের জন্য নাম নির্দিষ্ট করুন এবং প্রয়োজনীয় অনুমতিগুলি নির্ধারণ করুন।</p>
        </header>

        <form id="curpm-role-form">
            <div class="curpm-grid">
                
                <!-- Left Column: Role Details -->
                <div class="curpm-role-details">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #3159A3; margin-bottom: 20px;">রোলের তথ্য</h3>
                    
                    <div class="curpm-form-group">
                        <label for="curpm-role-name" class="curpm-label">রোলের নাম <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-name" name="role_name" class="curpm-input" placeholder="যেমন: Content Manager" required value="Content Manager">
                    </div>
                    
                    <div class="curpm-form-group">
                        <label for="curpm-role-slug" class="curpm-label">রোলের স্লাগ (সিস্টেম আইডি) <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-slug" name="role_slug" class="curpm-input" placeholder="যেমন: content-manager" required value="content-manager">
                    </div>

                    <div class="curpm-form-group">
                        <label for="curpm-role-description" class="curpm-label">বিবরণ</label>
                        <textarea id="curpm-role-description" name="role_description" class="curpm-textarea" rows="4" placeholder="রোলের প্রধান দায়িত্বগুলো সংক্ষেপে বর্ণনা করুন।"></textarea>
                    </div>

                    <div class="curpm-form-group" style="margin-top: 20px;">
                        <button type="submit" class="curpm-btn">রোল ও পারমিশন সংরক্ষণ করুন</button>
                    </div>
                </div>

                <!-- Right Column: Permissions Management -->
                <div class="curpm-permission-panel">
                    <h3>মডিউল পারমিশন</h3>
                    
                    <!-- Permission Modules (Accordion) -->
                    <div id="curpm-permission-accordion">
                        
                        <!-- Module 1: Content Management -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header active" data-content-id="module-content">
                                <span class="curpm-module-title">কন্টেন্ট ম্যানেজমেন্ট</span>
                                <span class="curpm-toggle-all" data-target-id="content-checkboxes" onclick="event.stopPropagation();">ডিসেলেক্ট অল</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content open" id="module-content">
                                <div class="curpm-permission-group" id="content-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-view" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">পোস্ট দেখুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-create" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">নতুন তৈরি করুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-own" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">নিজেরটি সম্পাদনা করুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-all" class="curpm-checkbox"><span class="curpm-permission-title">সব সম্পাদনা করুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-publish" class="curpm-checkbox"><span class="curpm-permission-title">প্রকাশ করুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-delete" class="curpm-checkbox"><span class="curpm-permission-title">মুছে ফেলুন</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 2: User Accounts -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" data-content-id="module-users">
                                <span class="curpm-module-title">ইউজার অ্যাকাউন্ট</span>
                                <span class="curpm-toggle-all" data-target-id="users-checkboxes" onclick="event.stopPropagation();">সিলেক্ট অল</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-users">
                                <div class="curpm-permission-group" id="users-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-view" class="curpm-checkbox"><span class="curpm-permission-title">ইউজার দেখুন</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-edit" class="curpm-checkbox"><span class="curpm-permission-title">প্রোফাইল সম্পাদনা</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-ban" class="curpm-checkbox"><span class="curpm-permission-title">ব্যান/আনব্যান</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 3: System Settings -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" data-content-id="module-settings">
                                <span class="curpm-module-title">সিস্টেম সেটিং</span>
                                <span class="curpm-toggle-all" data-target-id="settings-checkboxes" onclick="event.stopPropagation();">সিলেক্ট অল</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-settings">
                                <div class="curpm-permission-group" id="settings-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-general" class="curpm-checkbox"><span class="curpm-permission-title">সাধারণ কনফিগ</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-seo" class="curpm-checkbox"><span class="curpm-permission-title">SEO কনফিগ</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-roles" class="curpm-checkbox" checked><span class="curpm-permission-title">রোল পরিচালনা</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Permission Accordion -->

                </div>
            </div>
        </form>
    </div>

    <!-- Custom JavaScript using 'curpm_' prefix -->
    <script>
        // --- Custom Message Box Function (Replaces alert()) ---
        function curpm_showMessage(title, bodyHtml, type = 'success', duration = 5000) {
            const messageBox = document.getElementById('curpm-message-box');
            const titleEl = document.getElementById('curpm-message-title');
            const bodyEl = document.getElementById('curpm-message-body');
            
            // Set content and type
            titleEl.textContent = title;
            bodyEl.innerHTML = bodyHtml;

            messageBox.className = 'curpm-message-box'; // Reset
            messageBox.classList.add(type);
            messageBox.style.display = 'flex';
            
            // Hide after duration
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, duration);
        }

        // --- 1. Accordion Toggle Function ---
        function curpm_toggleAccordion(header) {
            const contentId = header.getAttribute('data-content-id');
            const content = document.getElementById(contentId);
            
            header.classList.toggle('active');

            // Toggle open state on content using class
            content.classList.toggle('open');
        }

        // --- 2. Select All/Deselect All Function ---
        function curpm_toggleModulePermissions(checkboxContainerId, selectAllButton) {
            const container = document.getElementById(checkboxContainerId);
            if (!container) return;
            
            const checkboxes = container.querySelectorAll('.curpm-checkbox');
            
            // Determine current state: If ALL are checked, we DESELECT ALL. Otherwise, we SELECT ALL.
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const newState = !allChecked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = newState;
            });
            
            // Update button text after toggling
            selectAllButton.textContent = newState ? 'Deselect All' : 'Select All';
        }

        // --- 3. Update Toggle Button Text on initial load and checkbox click ---
        function curpm_updateToggleText(containerId, button) {
            const checkboxes = document.querySelectorAll(`#${containerId} .curpm-checkbox`);
            if (checkboxes.length === 0 || !button) return;

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            // Check if ANY checkbox is checked to prevent "Select All" showing when none are checked
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);

            if (allChecked) {
                button.textContent = 'Deselect All';
            } else {
                button.textContent = 'Select All';
            }
        }
        
        // --- 4. Main Initialization and Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            // a) Initialize Accordion Headers
            document.querySelectorAll('.curpm-accordion-header').forEach(header => {
                // Prevent bubbling when clicking the inner toggle-all link, ensuring only the header toggle fires.
                header.addEventListener('click', function(e) {
                    // Only run if the click target is the header itself or the module title
                    if (e.target.classList.contains('curpm-accordion-header') || e.target.classList.contains('curpm-module-title') || e.target.classList.contains('curpm-accordion-icon')) {
                        curpm_toggleAccordion(this);
                    }
                });
            });

            // b) Initialize Toggle All Buttons
            document.querySelectorAll('.curpm-toggle-all').forEach(button => {
                const containerId = button.getAttribute('data-target-id');
                
                // Set initial text
                curpm_updateToggleText(containerId, button);
                
                // Add click listener
                button.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent the parent accordion header from closing
                    curpm_toggleModulePermissions(containerId, this);
                });
            });

            // c) Add listeners to all individual checkboxes to update the Toggle button text
            document.querySelectorAll('.curpm-permission-group').forEach(group => {
                const containerId = group.id;
                // Find the associated toggle button using the data-target-id
                const toggleButton = document.querySelector(`.curpm-toggle-all[data-target-id="${containerId}"]`);
                
                group.querySelectorAll('.curpm-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        curpm_updateToggleText(containerId, toggleButton);
                    });
                });
            });


            // d) Form Submission Handler
            document.getElementById('curpm-role-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const roleName = document.getElementById('curpm-role-name').value.trim();
                const roleSlug = document.getElementById('curpm-role-slug').value.trim();
                const roleDescription = document.getElementById('curpm-role-description').value.trim();

                // Simple validation
                if (!roleName || !roleSlug) {
                    curpm_showMessage(
                        'ভ্যালিডেশন ত্রুটি!', 
                        'রোলের নাম এবং রোলের স্লাগ অবশ্যই পূরণ করতে হবে।', 
                        'error'
                    );
                    return;
                }

                // Collect permissions
                const permissions = Array.from(document.querySelectorAll('.curpm-checkbox:checked'))
                                         .map(cb => cb.value);
                
                // Prepare message for display
                const messageHtml = `
                    <p><strong>রোলের নাম:</strong> ${roleName}</p>
                    <p><strong>রোলের স্লাগ:</strong> ${roleSlug}</p>
                    <p><strong>বিবরণ:</strong> ${roleDescription || 'নেই'}</p>
                    <p><strong>মোট পারমিশন দেওয়া হয়েছে:</strong> ${permissions.length}</p>
                    <p><strong>নির্বাচিত পারমিশনসমূহ:</strong></p>
                    <ul>
                        ${permissions.map(p => `<li>${p}</li>`).join('')}
                    </ul>
                `;
                
                curpm_showMessage(
                    'রোলের ডেটা সফলভাবে সংগ্রহ করা হয়েছে!', 
                    messageHtml, 
                    'success',
                    10000 // Show success message for 10 seconds
                );

                // In a real application, you would send this data via AJAX/fetch here.
                console.log({
                    roleName,
                    roleSlug,
                    roleDescription,
                    permissions
                });
            });
        });

    </script>
</body>
</html>


</x-admin-layout> --}}
<x-admin-layout>
    <x-slot name="page_title">
        Role Management
    </x-slot>

@php
    // কন্ট্রোলার থেকে আসা $role ভেরিয়েবল ব্যবহার করে ডেটা সেট করা হচ্ছে
    // $role->permissions মডেল ক্যাস্টিংয়ের কারণে সরাসরি PHP অ্যারে হিসাবে পাওয়া যাবে।
    $rolePermissions = old('permissions', $role->permissions ?? []);
    $formAction = $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store');
    $pageTitle = $role->exists ? 'Edit Role: ' . ($role->display_name ?? $role->name) : 'Create New Role';
    $roleDescription = old('description', $role->description ?? '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Base Styles */
        .curpm-body {
            font-family: 'Inter', sans-serif;
            background-color: #eef1f7; /* Very light, cool background */
            color: #2c3e50;
            padding: 40px 20px;
        }

        /* Container and Structure */
        .curpm-container {
            max-width: 1280px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .curpm-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #3159A3; /* Deep Blue Primary */
            margin-bottom: 5px;
        }
        
        .curpm-header p {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .curpm-grid {
            display: grid;
            grid-template-columns: 350px 1fr; /* Fixed width for details, flexible for permissions */
            gap: 40px;
        }

        /* Form Elements */
        .curpm-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3159A3;
            font-size: 0.95rem;
        }

        .curpm-input, .curpm-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fcfcfc;
            margin-bottom: 20px; /* Added spacing */
        }

        .curpm-input:focus, .curpm-textarea:focus {
            border-color: #3159A3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 89, 163, 0.15);
        }

        /* Submit Button */
        .curpm-btn {
            background: linear-gradient(135deg, #3159A3 0%, #4a77cc 100%);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 15px rgba(49, 89, 163, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .curpm-btn:hover {
            box-shadow: 0 5px 10px rgba(49, 89, 163, 0.6);
            transform: translateY(-2px);
        }
        
        .text-red-500 { color: #e53e3e; font-size: 0.85rem; margin-top: -15px; margin-bottom: 15px; }

        /* Permission Panel */
        .curpm-permission-panel {
            border-left: 1px solid #e0e0e0;
            padding-left: 40px;
        }
        
        .curpm-permission-panel h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 25px;
        }

        /* Accordion Items */
        .curpm-accordion-item {
            background-color: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        
        .curpm-accordion-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .curpm-accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .curpm-accordion-header:hover {
            background-color: #f5f8ff; /* Lightest blue on hover */
        }

        .curpm-module-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c3e50;
            flex-grow: 1;
        }

        /* Custom Toggle All Link */
        .curpm-toggle-all {
            font-size: 0.9rem;
            font-weight: 600;
            color: #3159A3;
            cursor: pointer;
            margin-right: 20px;
            text-transform: uppercase;
        }

        .curpm-accordion-icon {
            font-size: 24px;
            transition: transform 0.3s;
            color: #3159A3;
        }

        .curpm-accordion-header.active .curpm-accordion-icon {
            transform: rotate(180deg);
        }

        .curpm-accordion-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.55, 0, 0.1, 1), padding 0.4s ease-in-out;
            background-color: #f9f9f9;
        }
        
        .curpm-accordion-content.open {
            max-height: 800px; /* Increased for safety */
            padding: 20px;
            border-top: 1px solid #eee;
        }

        /* Permission Checkboxes - Custom Styling */
        .curpm-permission-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .curpm-checkbox-wrapper {
            display: flex;
            align-items: center;
            
        }

        .curpm-checkbox {
            /* Hide the default checkbox */
            opacity: 0;
            position: absolute;
        }

        .curpm-permission-title {
            position: relative;
            padding-left: 28px; /* Space for custom box */
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        /* Custom Checkbox Appearance */
        .curpm-permission-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border: 2px solid #a0aec0;
            border-radius: 4px;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .curpm-checkbox:checked + .curpm-permission-title::before {
            background-color: #3159A3;
            border-color: #3159A3;
        }

        .curpm-checkbox:checked + .curpm-permission-title::after {
            content: '\2713'; /* Checkmark symbol */
            position: absolute;
            top: 50%;
            left: 3px;
            transform: translateY(-50%);
            color: white;
            font-size: 14px;
            font-weight: 700;
        }

        /* Custom Message Box (Alert Replacement) */
        .curpm-message-box {
            position: fixed;
            top: 20px;
            right: 20px;
            display: none; /* Hidden by default */
            align-items: center;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .curpm-message-box.success {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            color: #155724;
        }
        .curpm-message-box.error {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        .curpm-message-box.info {
            background-color: #cce5ff;
            border-left: 5px solid #007bff;
            color: #004085;
        }

        .curpm-message-box strong {
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        .curpm-message-box ul {
            list-style: disc;
            margin-left: 20px;
            padding: 0;
        }

        /* Responsiveness */
        @media (max-width: 1000px) {
            .curpm-grid {
                grid-template-columns: 1fr;
            }
            .curpm-permission-panel {
                border-left: none;
                padding-left: 0;
                padding-top: 30px;
                margin-top: 30px;
                border-top: 1px solid #e0e0e0;
            }
        }
        
        @media (max-width: 600px) {
            .curpm-container {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="curpm-body">

    <div id="curpm-message-box" class="curpm-message-box">
        <div>
            <strong id="curpm-message-title"></strong>
            <div id="curpm-message-body"></div>
        </div>
    </div>
    <div class="curpm-container">
        <header class="curpm-header">
            <h1 id="curpm-page-title">{{ $pageTitle }}</h1>
            <p>Define the name and assign granular permissions for this role.</p>
        </header>

        {{-- for submit --}}
        <form id="curpm-role-form" action="{{ $formAction }}" method="POST">
            @csrf
            @if ($role->exists)
                @method('PUT')
            @endif
            
            <div class="curpm-grid">
                
                <div class="curpm-role-details">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #3159A3; margin-bottom: 20px;">Role Information</h3>
                    
                    {{-- Display Name --}}
                    <div class="curpm-form-group">
                        <label for="curpm-role-display-name" class="curpm-label">Role Display Name <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-display-name" name="display_name" class="curpm-input" placeholder="e.g., Content Manager" required value="{{ old('display_name', $role->display_name) }}">
                        @error('display_name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Role Slug (System ID/Name) --}}
                    <div class="curpm-form-group">
                        <label for="curpm-role-slug" class="curpm-label">Role Slug (System ID) <span style="color:#e53e3e;">*</span></label>
                        {{-- Super Admin রোল হলে স্লট পরিবর্তন করা উচিত নয় --}}
                        <input type="text" id="curpm-role-slug" name="name" class="curpm-input" placeholder="e.g., content-manager (Leave empty for auto-generation)" value="{{ old('name', $role->name) }}" {{ $role->exists && $role->name === 'super_admin' ? 'disabled' : '' }}>
                        @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="curpm-form-group">
                        <label for="curpm-role-description" class="curpm-label">Description</label>
                        <textarea id="curpm-role-description" name="description" class="curpm-textarea" rows="4" placeholder="Briefly describe the role's primary responsibilities.">{{ $roleDescription }}</textarea>
                    </div>

                    <div class="curpm-form-group" style="margin-top: 20px;">
                        <button type="submit" class="curpm-btn">
                            {{ $role->exists ? 'Update Role & Permissions' : 'Save Role & Permissions' }}
                        </button>
                    </div>
                </div>

                <div class="curpm-permission-panel">
                    <h3>Module Permissions</h3>
                    
                    <div id="curpm-permission-accordion">
                        
                        @foreach ($permissions as $module => $actions)
                            @php
                                $moduleId = Illuminate\Support\Str::slug(str_replace('_', '-', $module));
                                $displayModuleName = str_replace('_', ' ', $module);
                                // ভ্যালিডেশন ফেইল হলে অথবা প্রথম মডিউল হলে পারমিশন বক্স খোলা থাকবে
                                $contentOpen = ($errors->any() && array_filter($rolePermissions, fn($p) => str_starts_with($p, $module . '.'))) || $loop->first; 
                            @endphp
                            
                            <div class="curpm-accordion-item">
                                <div class="curpm-accordion-header @if ($contentOpen) active @endif" data-content-id="module-{{ $moduleId }}">
                                    <span class="curpm-module-title">{{ ucwords($displayModuleName) }}</span>
                                    <span class="curpm-toggle-all" data-target-id="{{ $moduleId }}-checkboxes" onclick="event.stopPropagation();">Select All</span>
                                    <span class="curpm-accordion-icon">&#9660;</span>
                                </div>
                                
                                <div class="curpm-accordion-content @if ($contentOpen) open @endif" id="module-{{ $moduleId }}">
                                    <div class="curpm-permission-group" id="{{ $moduleId }}-checkboxes">
                                        
                                        {{-- পারমিশন লুপ: $module.$action ফরম্যাটে --}}
                                        @foreach ($actions as $action)
                                            @php
                                                $permissionName = "{$module}.{$action}";
                                                // rolePermissions অ্যারেতে পারমিশন আছে কিনা চেক করা হচ্ছে
                                                $isChecked = in_array($permissionName, $rolePermissions); 
                                            @endphp
                                            
                                            <label class="curpm-checkbox-wrapper">
                                                <input 
                                                    type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permissionName }}" 
                                                    class="curpm-checkbox" 
                                                    {{ $isChecked ? 'checked' : '' }}
                                                >
                                                <span class="curpm-permission-title" title="{{ $permissionName }}">
                                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // --- Custom Message Box Function (Replaces alert() for better UI) ---
        function curpm_showMessage(title, bodyHtml, type = 'success', duration = 5000) {
            const messageBox = document.getElementById('curpm-message-box');
            const titleEl = document.getElementById('curpm-message-title');
            const bodyEl = document.getElementById('curpm-message-body');
            
            // Set content and type
            titleEl.textContent = title;
            bodyEl.innerHTML = bodyHtml;

            messageBox.className = 'curpm-message-box'; // Reset classes
            messageBox.classList.add(type);
            messageBox.style.display = 'flex';
            
            // Hide after duration
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, duration);
        }

        // --- Accordion Toggle Function ---
        function curpm_toggleAccordion(header) {
            const contentId = header.getAttribute('data-content-id');
            const content = document.getElementById(contentId);
            
            header.classList.toggle('active');

            // Toggle open state on content using class
            content.classList.toggle('open');
        }

        // --- Select All/Deselect All Function ---
        function curpm_toggleModulePermissions(checkboxContainerId, selectAllButton) {
            const container = document.getElementById(checkboxContainerId);
            if (!container) return;
            
            const checkboxes = container.querySelectorAll('.curpm-checkbox');
            
            // Determine current state: If ALL are checked, we DESELECT ALL. Otherwise, we SELECT ALL.
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const newState = !allChecked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = newState;
            });
            
            // Update button text
            selectAllButton.textContent = newState ? 'Deselect All' : 'Select All';
        }

        // --- Update Toggle Button Text on initial load and checkbox click ---
        function curpm_updateToggleText(containerId, button) {
            const checkboxes = document.querySelectorAll(`#${containerId} .curpm-checkbox`);
            if (checkboxes.length === 0 || !button) return;

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            button.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
        
        // --- Main Initialization and Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {

            // a) Initialize Accordion Headers
            document.querySelectorAll('.curpm-accordion-header').forEach(header => {
                // Toggle All বাটনে ক্লিক হলে যাতে অ্যাকর্ডিয়ন ট্রিগার না হয়, তার জন্য আলাদা ইভেন্ট লিসেনার
                const toggleButton = header.querySelector('.curpm-toggle-all');
                if (toggleButton) {
                    toggleButton.addEventListener('click', function(e) {
                        e.stopPropagation(); // এটি ছাড়া Accordion header ইভেন্টও ফায়ার হবে
                    });
                }
                
                header.addEventListener('click', function() {
                    curpm_toggleAccordion(this);
                });
            });

            // b) Initialize Toggle All Buttons
            document.querySelectorAll('.curpm-toggle-all').forEach(button => {
                const containerId = button.getAttribute('data-target-id');
                // Set initial text
                curpm_updateToggleText(containerId, button);
                
                // Add click listener
                button.addEventListener('click', function(e) {
                    curpm_toggleModulePermissions(containerId, this);
                });
            });

            // c) Add listeners to all individual checkboxes to update the Toggle button text
            document.querySelectorAll('.curpm-permission-group').forEach(group => {
                const containerId = group.id;
                const toggleButton = document.querySelector(`.curpm-toggle-all[data-target-id="${containerId}"]`);
                
                group.querySelectorAll('.curpm-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        curpm_updateToggleText(containerId, toggleButton);
                    });
                });
            });


            // d) Flash messages from Laravel (Success/Error/Validation)
            @if (session('success'))
                curpm_showMessage('Success!', '{{ session('success') }}', 'success', 7000);
            @endif

            // ভ্যালিডেশন এরর হ্যান্ডলিং
            @if ($errors->any())
                let errorHtml = '<ul>';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';
                curpm_showMessage('Validation Error!', errorHtml, 'error', 10000);
            @endif
            
            // e) Role Display Name থেকে Role Slug অটো-জেনারেট করা (শুধুমাত্র Create মোডে)
            const displayNameInput = document.getElementById('curpm-role-display-name');
            const slugInput = document.getElementById('curpm-role-slug');
            
            if (displayNameInput && slugInput && !slugInput.hasAttribute('disabled')) {
                // ডিসপ্লে নাম পরিবর্তন হলে স্লগ আপডেট করা 
                displayNameInput.addEventListener('input', function() {
                    // যদি স্লগ ইনপুটটি খালি থাকে বা পূর্বে কোনো ভ্যালু না থাকে
                    if (!slugInput.value) {
                        // স্ট্রিংটিকে ল্যাটিন ক্যারেক্টারে পরিবর্তন করে স্লগ তৈরি (আপনি Laravel-এর Str::slug লজিক এখানে ব্যবহার করতে পারবেন না, তাই একটি সাধারণ জাভাস্ক্রিপ্ট স্লগিফাই ব্যবহার করা হচ্ছে)
                        let slug = this.value.toLowerCase()
                            .trim()
                            .replace(/[^\w\s-]/g, '') // নন-ওয়ার্ড ক্যারেক্টার ও স্পেস সরানো
                            .replace(/[\s_-]+/g, '_') // স্পেস বা ড্যাশকে আন্ডারস্কোরে পরিবর্তন করা 
                            .replace(/^-+|-+$/g, ''); // শুরুতে বা শেষে ড্যাশ সরানো
                            
                        slugInput.value = slug;
                    }
                });
                
                // স্লগ ইনপুট এডিট করা হলে, অটো-জেনারেট বন্ধ করে দেওয়া
                slugInput.addEventListener('input', function() {
                    // শুধু যদি স্লগটি ম্যানুয়ালি এডিট করা হয়
                    if (this.value) {
                        displayNameInput.removeEventListener('input', arguments.callee); // অটো-জেনারেট লিসেনার বন্ধ করা
                    }
                });
            }
        });
    </script>
</body>
</x-admin-layout>