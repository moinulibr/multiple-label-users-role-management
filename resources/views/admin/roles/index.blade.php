{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Permission Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS using 'curpm-' prefix for isolation -->
    <style>
        /* General Reset and Font */
        .curpm-body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 20px;
        }

        /* Container and Card Styles */
        .curpm-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .curpm-header {
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .curpm-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
        }

        .curpm-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        /* Form Controls */
        .curpm-form-group {
            margin-bottom: 20px;
        }

        .curpm-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }

        .curpm-input, .curpm-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .curpm-input:focus, .curpm-textarea:focus {
            border-color: #4c51bf;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 81, 191, 0.2);
        }

        /* Button Style */
        .curpm-btn {
            background-color: #4c51bf; /* Indigo/Violet */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            box-shadow: 0 4px 10px rgba(76, 81, 191, 0.3);
        }

        .curpm-btn:hover {
            background-color: #5a62c4;
            transform: translateY(-1px);
        }

        /* Permission Area - Accordion Style */
        .curpm-permission-area h3 {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .curpm-accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
            background-color: #fff;
        }

        .curpm-accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #f7fafc;
            cursor: pointer;
            border-bottom: 1px solid transparent;
            transition: background-color 0.2s;
        }
        
        .curpm-accordion-header:hover {
            background-color: #edf2f7;
        }

        .curpm-module-title {
            font-weight: 700;
            font-size: 18px;
            color: #2d3748;
        }

        .curpm-accordion-icon {
            font-size: 20px;
            transition: transform 0.3s;
        }

        .curpm-accordion-header.active .curpm-accordion-icon {
            transform: rotate(180deg);
        }

        .curpm-accordion-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out;
        }
        
        .curpm-accordion-content.open {
            max-height: 300px; /* Needs to be large enough to contain content */
            padding: 15px 20px 20px;
        }

        /* Permission Checkboxes */
        .curpm-permission-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding-top: 10px;
        }

        .curpm-checkbox-wrapper {
            display: flex;
            align-items: center;
            width: 150px; /* Fixed width for better layout control */
        }

        .curpm-checkbox {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #a0aec0;
            border-radius: 4px;
            margin-right: 8px;
            cursor: pointer;
            position: relative;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .curpm-checkbox:checked {
            background-color: #4c51bf;
            border-color: #4c51bf;
        }

        .curpm-checkbox:checked::after {
            content: '\2713'; /* Checkmark symbol */
            color: white;
            font-size: 14px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .curpm-permission-title {
            font-weight: 500;
            font-size: 15px;
        }

        .curpm-select-all {
            font-size: 14px;
            color: #4c51bf;
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s;
        }

        .curpm-select-all:hover {
            color: #3a3f9e;
        }

        /* Responsiveness */
        @media (max-width: 900px) {
            .curpm-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 500px) {
            .curpm-permission-group {
                flex-direction: column;
                gap: 10px;
            }
            .curpm-checkbox-wrapper {
                width: 100%;
            }
        }
    </style>
</head>

<body class="curpm-body">

    <div class="curpm-container">
        <header class="curpm-header">
            <!-- This will dynamically change based on Create or Edit context -->
            <h1 id="curpm-page-title">Create New Role</h1>
        </header>

        <form id="curpm-role-form" action="#" method="POST">
            <div class="curpm-grid">
                
                <!-- Left Column: Role Details -->
                <div>
                    <h3 class="curpm-label" style="font-size: 20px; margin-bottom: 15px;">Role Details</h3>
                    <div class="curpm-form-group">
                        <label for="curpm-role-name" class="curpm-label">Role Name <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-name" name="role_name" class="curpm-input" placeholder="e.g., Administrator, Editor, User" required value="Editor">
                    </div>
                    
                    <div class="curpm-form-group">
                        <label for="curpm-role-slug" class="curpm-label">Role Slug (System ID) <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-slug" name="role_slug" class="curpm-input" placeholder="e.g., editor" required value="editor">
                    </div>

                    <div class="curpm-form-group">
                        <label for="curpm-role-description" class="curpm-label">Description</label>
                        <textarea id="curpm-role-description" name="role_description" class="curpm-textarea" rows="3" placeholder="Briefly describe the role's primary responsibilities."></textarea>
                    </div>

                    <div class="curpm-form-group" style="margin-top: 30px;">
                        <button type="submit" class="curpm-btn">Save Role & Permissions</button>
                    </div>
                </div>

                <!-- Right Column: Permissions Management -->
                <div class="curpm-permission-area">
                    <h3 style="margin-top: 0;">Permissions</h3>

                    <p style="color: #718096; margin-bottom: 25px;">Select the specific actions this role is authorized to perform across different modules.</p>

                    <!-- Permission Modules (Accordion) -->
                    <div id="curpm-permission-accordion">
                        
                        <!-- Module 1: Users Management -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion('module-users')">
                                <span class="curpm-module-title">User Management</span>
                                <span class="curpm-select-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('module-users', 'users-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content open" id="module-users">
                                <div class="curpm-permission-group" id="users-checkboxes">
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="user-view" class="curpm-checkbox curpm-module-users" checked><span class="curpm-permission-title">View</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="user-create" class="curpm-checkbox curpm-module-users" checked><span class="curpm-permission-title">Create</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="user-edit" class="curpm-checkbox curpm-module-users" checked><span class="curpm-permission-title">Edit</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="user-delete" class="curpm-checkbox curpm-module-users"><span class="curpm-permission-title">Delete</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="user-status" class="curpm-checkbox curpm-module-users"><span class="curpm-permission-title">Change Status</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 2: Product Catalog -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion('module-products')">
                                <span class="curpm-module-title">Product Catalog</span>
                                <span class="curpm-select-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('module-products', 'products-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-products">
                                <div class="curpm-permission-group" id="products-checkboxes">
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="product-view" class="curpm-checkbox curpm-module-products"><span class="curpm-permission-title">View</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="product-create" class="curpm-checkbox curpm-module-products"><span class="curpm-permission-title">Create</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="product-edit" class="curpm-checkbox curpm-module-products"><span class="curpm-permission-title">Edit</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="product-delete" class="curpm-checkbox curpm-module-products"><span class="curpm-permission-title">Delete</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 3: Orders Processing -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion('module-orders')">
                                <span class="curpm-module-title">Orders Processing</span>
                                <span class="curpm-select-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('module-orders', 'orders-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-orders">
                                <div class="curpm-permission-group" id="orders-checkboxes">
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="order-view" class="curpm-checkbox curpm-module-orders" checked><span class="curpm-permission-title">View</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="order-process" class="curpm-checkbox curpm-module-orders"><span class="curpm-permission-title">Process</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="order-cancel" class="curpm-checkbox curpm-module-orders"><span class="curpm-permission-title">Cancel</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 4: Site Settings -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion('module-settings')">
                                <span class="curpm-module-title">Site Settings</span>
                                <span class="curpm-select-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('module-settings', 'settings-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-settings">
                                <div class="curpm-permission-group" id="settings-checkboxes">
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="setting-general" class="curpm-checkbox curpm-module-settings"><span class="curpm-permission-title">General</span></label>
                                    <label class="curpm-checkbox-wrapper"><input type="checkbox" name="permissions[]" value="setting-email" class="curpm-checkbox curpm-module-settings"><span class="curpm-permission-title">Email</span></label>
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
        // 1. Accordion Toggle Function
        function curpm_toggleAccordion(contentId) {
            const content = document.getElementById(contentId);
            const header = content.previousElementSibling;

            // Toggle active state on header
            header.classList.toggle('active');

            // Toggle open state on content
            content.classList.toggle('open');
        }

        // 2. Select All/Deselect All Function
        function curpm_toggleModulePermissions(moduleId, checkboxContainerId, selectAllButton) {
            const container = document.getElementById(checkboxContainerId);
            const checkboxes = container.querySelectorAll('.curpm-checkbox');
            
            // Check current state of checkboxes (if any are unchecked, we select all; otherwise, we deselect all)
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const newState = !allChecked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = newState;
            });
            
            // Update button text
            selectAllButton.textContent = newState ? 'Deselect All' : 'Select All';
        }

        // 3. Initial Setup and Simulation (On page load)
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state for the first module's select-all button text
            const userCheckboxes = document.querySelectorAll('#users-checkboxes .curpm-checkbox');
            const allUsersChecked = Array.from(userCheckboxes).every(cb => cb.checked);
            const selectAllButton = document.querySelector('#module-users').previousElementSibling.querySelector('.curpm-select-all');
            
            if (selectAllButton) {
                selectAllButton.textContent = allUsersChecked ? 'Deselect All' : 'Select All';
            }
            
            // Optional: Form Submission Handler (for demo purposes)
            document.getElementById('curpm-role-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const roleName = document.getElementById('curpm-role-name').value;
                const permissions = Array.from(document.querySelectorAll('.curpm-checkbox:checked'))
                                       .map(cb => cb.value);
                
                alert(`Saving Role: ${roleName}\nPermissions Assigned: ${permissions.join(', ')}`);
                // In a real application, you would send this data to the server.
            });
        });

    </script>
</body>
</html> --}}

<x-admin-layout>
  
    {{-- পেজের টাইটেল (header নামে Named Slot) --}}
    <x-slot name="page_title">
        Roles List
    </x-slot>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List | Permission Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* [Core CSS Content - Copied from user's provided HTML] */
        
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

        .curpm-header {
            margin-bottom: 25px;
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
        }

        /* --- Form & Input Elements --- */
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
        }

        .curpm-input:focus, .curpm-textarea:focus {
            border-color: #3159A3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 89, 163, 0.15);
        }

        /* --- Button Styles --- */
        .curpm-btn {
            background: linear-gradient(135deg, #3159A3 0%, #4a77cc 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 10px rgba(49, 89, 163, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .curpm-btn:hover {
            box-shadow: 0 3px 8px rgba(49, 89, 163, 0.6);
            transform: translateY(-1px);
        }

        .curpm-btn-secondary {
            background: #e0e0e0;
            color: #555;
            box-shadow: none;
        }

        .curpm-btn-secondary:hover {
            background: #d0d0d0;
            transform: translateY(-1px);
        }

        .curpm-btn-action { /* Small action buttons for table rows */
            padding: 6px 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
            margin-left: 5px;
            text-transform: capitalize;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: opacity 0.2s;
        }
        .curpm-btn-action:hover {
            opacity: 0.8;
        }

        .curpm-btn-edit { background-color: #f39c12; }
        .curpm-btn-delete { background-color: #c0392b; }


        /* --- Index Page Table Styles (curpm-index-table) --- */
        .curpm-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fb;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .curpm-search-bar {
            padding: 10px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
        }

        .curpm-index-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .curpm-index-table th, .curpm-index-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .curpm-index-table th {
            background-color: #3159A3;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .curpm-index-table tr:last-child td {
            border-bottom: none;
        }

        .curpm-index-table tbody tr:hover {
            background-color: #f5f8ff;
        }

        .curpm-role-slug {
            font-family: monospace;
            background-color: #e8eaf6;
            color: #3949ab;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .curpm-role-status-active {
            background-color: #e6f7ee;
            color: #27ae60;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Pagination (Simple Demo) */
        .curpm-pagination {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .curpm-pagination-link {
            display: block;
            padding: 8px 14px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            text-decoration: none;
            color: #3159A3;
            transition: background-color 0.2s, color 0.2s;
        }

        .curpm-pagination-link:hover, .curpm-pagination-link.active {
            background-color: #3159A3;
            color: white;
            border-color: #3159A3;
        }
        
        /* Responsiveness */
        @media (max-width: 1000px) {
            .curpm-grid { grid-template-columns: 1fr; }
            .curpm-permission-panel { border-left: none; padding-left: 0; padding-top: 30px; margin-top: 30px; border-top: 1px solid #e0e0e0; }
        }
        @media (max-width: 600px) {
            .curpm-container { padding: 20px; }
            .curpm-actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .curpm-search-bar {
                width: 100%;
                margin-bottom: 15px;
            }
            .curpm-index-table thead {
                display: none; /* Hide table headers on mobile */
            }
            .curpm-index-table, .curpm-index-table tbody, .curpm-index-table tr, .curpm-index-table td {
                display: block;
                width: 100%;
            }
            .curpm-index-table tr {
                margin-bottom: 15px;
                border: 1px solid #dcdfe6;
                border-radius: 10px;
            }
            .curpm-index-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .curpm-index-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: 600;
                color: #3159A3;
            }
        }
    </style>
</head>

<body class="curpm-body">

    <div class="curpm-container">
        <!-- Success/Error Message Display (Laravel Session Flash) -->
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <header class="curpm-header">
            <h1>Roles List</h1>
            <p>View all user roles in the system. You can create new roles and edit existing ones here.</p>
        </header>

        <div class="curpm-actions-bar">
            <input type="text" class="curpm-search-bar" id="curpm-search-input" placeholder="Search by Role Name or Slug...">
            
            <!-- Create Link (using Laravel route helper) -->
            <a href="{{ route('admin.roles.create') }}" class="curpm-btn curpm-btn-create">
                Create New Role
            </a>
        </div>

        <table class="curpm-index-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Slug</th>
                    <th>User Count</th>
                    <th>Permissions Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Blade loop to iterate over the roles passed from the controller -->
                @forelse($roles as $index => $role)
                <tr>
                    <td data-label="#">{{ $index + 1 }}</td>
                    <td data-label="Role Name">{{ $role->display_name }}</td>
                    <td data-label="Slug"><span class="curpm-role-slug">{{ $role->name }}</span></td>
                    {{-- User Count is mock/placeholder data here. In a real app, you'd use $role->users()->count() --}}
                    <td data-label="User Count">
                        @if ($role->name === 'super_admin')
                            ∞
                        @else
                            {{ rand(0, 15) }} 
                        @endif
                    </td>
                    {{-- Permissions Count (assuming 'permissions' is an array field) --}}
                    <td data-label="Permissions Count"><span class="curpm-role-status-active">{{ count($role->permissions ?? []) }}</span></td>
                    <td data-label="Action">
                        <!-- Edit Button (linked to edit route) -->
                        <a href="{{ route('admin.roles.edit', $role) }}" class="curpm-btn-action curpm-btn-edit">Edit</a>
                        
                        <!-- Delete Form (using a standard Laravel form with custom confirmation) -->
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return curpm_confirmDelete(this, '{{ $role->display_name }}', '{{ $role->name }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="curpm-btn-action curpm-btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No roles found. Click "Create New Role" to add one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links (if using Role::paginate() in controller) -->
        {{-- <div class="curpm-pagination">
            {{ $roles->links() }}
            <a href="#" class="curpm-pagination-link">&laquo; Previous</a>
            <a href="#" class="curpm-pagination-link active">1</a>
            <a href="#" class="curpm-pagination-link">Next &raquo;</a>
        </div> --}}
    </div>
    
    <script>
        // Custom confirmation function to replace alert()
        function curpm_confirmDelete(form, displayName, slugName) {
            if (slugName === 'super_admin') {
                curpm_showMessageModal(`Cannot Delete Role`, `The <strong>Super Admin</strong> role cannot be deleted from the system.`, 'error');
                return false;
            }
            
            curpm_showConfirmModal(`Confirm Deletion`, 
                `Are you sure you want to delete the role <strong>${displayName}</strong>? This action cannot be undone.`, 
                () => { form.submit(); } // If confirmed, submit the form
            );
            return false; // Prevent default form submission until confirmed
        }
        
        // --- Modal/Message Box Implementation (for error/confirmation) ---
        // I created a simple modal structure to avoid using window.alert/confirm
        function curpm_showMessageModal(title, message, type = 'info') {
            let modal = document.getElementById('curpm-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'curpm-modal';
                modal.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 10000;">
                        <div id="curpm-modal-content" style="background: white; padding: 25px; border-radius: 10px; max-width: 400px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                            <h3 id="curpm-modal-title" style="margin-top: 0; font-size: 1.2rem; font-weight: 700;">Title</h3>
                            <p id="curpm-modal-body" style="margin-bottom: 20px;"></p>
                            <div style="text-align: right;">
                                <button onclick="this.closest('#curpm-modal').remove()" class="curpm-btn-secondary" style="padding: 8px 15px; border-radius: 6px; font-weight: 600;">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            document.getElementById('curpm-modal-title').innerHTML = title;
            document.getElementById('curpm-modal-body').innerHTML = message;
            modal.querySelector('#curpm-modal-content').style.borderTop = type === 'error' ? '5px solid #c0392b' : '5px solid #3159A3';
        }
        
        function curpm_showConfirmModal(title, message, callback) {
            let modal = document.getElementById('curpm-confirm-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'curpm-confirm-modal';
                modal.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 10000;">
                        <div id="curpm-confirm-modal-content" style="background: white; padding: 25px; border-radius: 10px; max-width: 400px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border-top: 5px solid #f39c12;">
                            <h3 id="curpm-confirm-modal-title" style="margin-top: 0; font-size: 1.2rem; font-weight: 700;">Title</h3>
                            <p id="curpm-confirm-modal-body" style="margin-bottom: 20px;"></p>
                            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                <button id="curpm-confirm-cancel" class="curpm-btn-secondary" style="padding: 8px 15px; border-radius: 6px; font-weight: 600;">Cancel</button>
                                <button id="curpm-confirm-ok" class="curpm-btn-delete curpm-btn-action" style="padding: 8px 15px; border-radius: 6px; font-weight: 600;">Delete</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            document.getElementById('curpm-confirm-modal-title').innerHTML = title;
            document.getElementById('curpm-confirm-modal-body').innerHTML = message;

            const okButton = document.getElementById('curpm-confirm-ok');
            const cancelButton = document.getElementById('curpm-confirm-cancel');
            okButton.onclick = null;
            cancelButton.onclick = null;

            okButton.onclick = () => {
                callback();
                modal.remove();
            };
            cancelButton.onclick = () => {
                modal.remove();
            };
        }
        
        // Search Function (Front-end filtering)
        document.getElementById('curpm-search-input').addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const table = document.querySelector('.curpm-index-table tbody');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                // Role Name (index 1) and Slug (index 2) columns
                const nameCol = rows[i].getElementsByTagName('td')[1];
                const slugCol = rows[i].getElementsByTagName('td')[2];
                
                if (nameCol || slugCol) {
                    const nameText = nameCol.textContent || nameCol.innerText;
                    const slugText = slugCol.textContent || slugCol.innerText;
                    
                    if (nameText.toUpperCase().indexOf(filter) > -1 || slugText.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    </script>
</body>
</html>

</x-admin-layout>




