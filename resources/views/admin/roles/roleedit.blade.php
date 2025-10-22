{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role | Permission Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* [Core CSS Content Here] */
        /* To ensure the file is self-contained, the Core CSS is repeated here. */

        /* Base Styles */
        .curpm-body {
            font-family: 'Inter', sans-serif;
            background-color: #eef1f7;
            /* Very light, cool background */
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
            color: #3159A3;
            /* Deep Blue Primary */
            margin-bottom: 5px;
        }

        .curpm-header p {
            font-size: 1rem;
            color: #7f8c8d;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* --- Form & Input Elements --- */
        .curpm-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3159A3;
            font-size: 0.95rem;
        }

        .curpm-input,
        .curpm-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fcfcfc;
        }

        .curpm-input:focus,
        .curpm-textarea:focus {
            border-color: #3159A3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 89, 163, 0.15);
        }

        /* --- Button Styles --- */
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

        .curpm-btn-secondary {
            background: #e0e0e0;
            color: #555;
            box-shadow: none;
            padding: 14px 30px;
            /* Matching size */
            margin-right: 15px;
        }

        .curpm-btn-secondary:hover {
            background: #d0d0d0;
            transform: translateY(-2px);
        }

        /* --- Edit/Create Page Specific --- */
        .curpm-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
        }

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
            background-color: #f5f8ff;
            /* Lightest blue on hover */
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
            max-height: 600px;
            /* Increased for safety */
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
            opacity: 0;
            position: absolute;
        }

        .curpm-permission-title {
            position: relative;
            padding-left: 28px;
            /* Space for custom box */
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

        .curpm-checkbox:checked+.curpm-permission-title::before {
            background-color: #3159A3;
            border-color: #3159A3;
        }

        .curpm-checkbox:checked+.curpm-permission-title::after {
            content: '\2713';
            /* Checkmark symbol */
            position: absolute;
            top: 50%;
            left: 3px;
            transform: translateY(-50%);
            color: white;
            font-size: 14px;
            font-weight: 700;
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
        }
    </style>
</head>

<body class="curpm-body">

    <div class="curpm-container">
        <header class="curpm-header">
            <h1 id="curpm-page-title">Edit Role: Content Manager</h1>
            <p>Modify the role's details and assign new permissions or remove existing ones.</p>
        </header>

        <form id="curpm-role-form" action="#" method="POST">
            <div class="curpm-grid">

                <!-- Left Column: Role Details -->
                <div class="curpm-role-details">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #3159A3; margin-bottom: 20px;">Role Information</h3>

                    <div class="curpm-form-group">
                        <label for="curpm-role-name" class="curpm-label">Role Name <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-name" name="role_name" class="curpm-input" required value="Content Manager (Level 2)">
                    </div>

                    <div class="curpm-form-group">
                        <label for="curpm-role-slug" class="curpm-label">Role Slug (System ID) <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-slug" name="role_slug" class="curpm-input" required value="content-manager" disabled style="background-color:#eee;">
                        <small style="color:#7f8c8d; margin-top: 5px; display: block;">Slug usually cannot be edited.</small>
                    </div>

                    <div class="curpm-form-group">
                        <label for="curpm-role-description" class="curpm-label">Description</label>
                        <textarea id="curpm-role-description" name="role_description" class="curpm-textarea" rows="4">Responsible for creating, editing, and publishing assigned content.</textarea>
                    </div>

                    <div class="curpm-form-group" style="margin-top: 40px;">
                        <button type="button" class="curpm-btn-secondary" onclick="window.location.href='role_index.html'">Back</button>
                        <button type="submit" class="curpm-btn">Save Changes</button>
                    </div>
                </div>

                <!-- Right Column: Permissions Management -->
                <div class="curpm-permission-panel">
                    <h3>Module Permissions</h3>

                    <!-- Permission Modules (Accordion) -->
                    <div id="curpm-permission-accordion">

                        <!-- Module 1: Content Management -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header active" onclick="curpm_toggleAccordion(this, 'module-content')">
                                <span class="curpm-module-title">Content Management</span>
                                <span class="curpm-toggle-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('content-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content open" id="module-content">
                                <div class="curpm-permission-group" id="content-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-view" class="curpm-checkbox curpm-module-content" checked>
                                        <span class="curpm-permission-title">View Posts</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-create" class="curpm-checkbox curpm-module-content" checked>
                                        <span class="curpm-permission-title">Create New</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-own" class="curpm-checkbox curpm-module-content" checked>
                                        <span class="curpm-permission-title">Edit Own</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-all" class="curpm-checkbox curpm-module-content"><span class="curpm-permission-title">Edit All</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-publish" class="curpm-checkbox curpm-module-content" checked>
                                        <span class="curpm-permission-title">Publish</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-delete" class="curpm-checkbox curpm-module-content"><span class="curpm-permission-title">Delete</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 2: User Accounts (Checked based on existing role) -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion(this, 'module-users')">
                                <span class="curpm-module-title">User Accounts</span>
                                <span class="curpm-toggle-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('users-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-users">
                                <div class="curpm-permission-group" id="users-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-view" class="curpm-checkbox curpm-module-users" checked>
                                        <span class="curpm-permission-title">View Users</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-edit" class="curpm-checkbox curpm-module-users" checked>
                                        <span class="curpm-permission-title">Edit Profile</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-ban" class="curpm-checkbox curpm-module-users"><span class="curpm-permission-title">Ban/Unban</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 3: System Settings -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" onclick="curpm_toggleAccordion(this, 'module-settings')">
                                <span class="curpm-module-title">System Settings</span>
                                <span class="curpm-toggle-all" onclick="event.stopPropagation(); curpm_toggleModulePermissions('settings-checkboxes', this);">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-settings">
                                <div class="curpm-permission-group" id="settings-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-general" class="curpm-checkbox curpm-module-settings"><span class="curpm-permission-title">General Config</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-seo" class="curpm-checkbox curpm-module-settings"><span class="curpm-permission-title">SEO Config</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-roles" class="curpm-checkbox curpm-module-settings" checked>
                                        <span class="curpm-permission-title">Manage Roles</span>
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
        // 1. Accordion Toggle Function
        function curpm_toggleAccordion(header, contentId) {
            const content = document.getElementById(contentId);

            header.classList.toggle('active');
            content.classList.toggle('open');
        }

        // 2. Select All/Deselect All Function
        function curpm_toggleModulePermissions(checkboxContainerId, selectAllButton) {
            const container = document.getElementById(checkboxContainerId);
            const checkboxes = container.querySelectorAll('.curpm-checkbox');

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const newState = !allChecked;

            checkboxes.forEach(checkbox => {
                checkbox.checked = newState;
            });

            selectAllButton.textContent = newState ? 'Deselect All' : 'Select All';
        }

        // 3. Initial Setup (Set Select All button text on load)
        document.addEventListener('DOMContentLoaded', function() {
            // Function to check initial state and update 'Select All' text
            function updateInitialToggleText(containerId, button) {
                const checkboxes = document.querySelectorAll(`#${containerId} .curpm-checkbox`);
                if (checkboxes.length === 0) return;

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                button.textContent = allChecked ? 'Deselect All' : 'Select All';
            }

            // Apply to all modules
            const modules = [{
                    id: 'content-checkboxes',
                    btn: document.querySelector('#module-content').previousElementSibling.querySelector('.curpm-toggle-all')
                },
                {
                    id: 'users-checkboxes',
                    btn: document.querySelector('#module-users').previousElementSibling.querySelector('.curpm-toggle-all')
                },
                {
                    id: 'settings-checkboxes',
                    btn: document.querySelector('#module-settings').previousElementSibling.querySelector('.curpm-toggle-all')
                }
            ];

            modules.forEach(m => {
                if (m.btn) {
                    updateInitialToggleText(m.id, m.btn);
                }
            });

            // Optional: Form Submission Handler (for demo purposes)
            document.getElementById('curpm-role-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const roleName = document.getElementById('curpm-role-name').value;
                const permissions = Array.from(document.querySelectorAll('.curpm-checkbox:checked'))
                    .map(cb => cb.value);

                alert(`Role: ${roleName}\nUpdated Permissions: ${permissions.join(', ')}`);
            });
        });
    </script>
</body>

</html> --}}



<x-admin-layout>
  
    <x-slot name="page_title">
        Role Crate
    </x-slot>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Role & Permission Management</title>
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
            .curpm-permission-group {
                flex-direction: column;
                gap: 15px;
            }
            .curpm-checkbox-wrapper {
                width: 100%;
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
            <h1 id="curpm-page-title">Create New Role</h1>
            <p>Define the name and assign granular permissions for this role.</p>
        </header>

        <form id="curpm-role-form">
            <div class="curpm-grid">
                
                <!-- Left Column: Role Details -->
                <div class="curpm-role-details">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #3159A3; margin-bottom: 20px;">Role Information</h3>
                    
                    <div class="curpm-form-group">
                        <label for="curpm-role-name" class="curpm-label">Role Name <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-name" name="role_name" class="curpm-input" placeholder="e.g., Content Manager" required value="Content Manager">
                    </div>
                    
                    <div class="curpm-form-group">
                        <label for="curpm-role-slug" class="curpm-label">Role Slug (System ID) <span style="color:#e53e3e;">*</span></label>
                        <input type="text" id="curpm-role-slug" name="role_slug" class="curpm-input" placeholder="e.g., content-manager" required value="content-manager">
                    </div>

                    <div class="curpm-form-group">
                        <label for="curpm-role-description" class="curpm-label">Description</label>
                        <textarea id="curpm-role-description" name="role_description" class="curpm-textarea" rows="4" placeholder="Briefly describe the role's primary responsibilities."></textarea>
                    </div>

                    <div class="curpm-form-group" style="margin-top: 20px;">
                        <button type="submit" class="curpm-btn">Save Role & Permissions</button>
                    </div>
                </div>

                <!-- Right Column: Permissions Management -->
                <div class="curpm-permission-panel">
                    <h3>Module Permissions</h3>
                    
                    <!-- Permission Modules (Accordion) -->
                    <div id="curpm-permission-accordion">
                        
                        <!-- Module 1: Content Management -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header active" data-content-id="module-content">
                                <span class="curpm-module-title">Content Management</span>
                                <span class="curpm-toggle-all" data-target-id="content-checkboxes">Deselect All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content open" id="module-content">
                                <div class="curpm-permission-group" id="content-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-view" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">View Posts</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-create" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">Create New</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-own" class="curpm-checkbox" checked>
                                        <span class="curpm-permission-title">Edit Own</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-edit-all" class="curpm-checkbox"><span class="curpm-permission-title">Edit All</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-publish" class="curpm-checkbox"><span class="curpm-permission-title">Publish</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="content-delete" class="curpm-checkbox"><span class="curpm-permission-title">Delete</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 2: User Accounts -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" data-content-id="module-users">
                                <span class="curpm-module-title">User Accounts</span>
                                <span class="curpm-toggle-all" data-target-id="users-checkboxes">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-users">
                                <div class="curpm-permission-group" id="users-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-view" class="curpm-checkbox"><span class="curpm-permission-title">View Users</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-edit" class="curpm-checkbox"><span class="curpm-permission-title">Edit Profile</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="user-ban" class="curpm-checkbox"><span class="curpm-permission-title">Ban/Unban</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Module 3: System Settings -->
                        <div class="curpm-accordion-item">
                            <div class="curpm-accordion-header" data-content-id="module-settings">
                                <span class="curpm-module-title">System Settings</span>
                                <span class="curpm-toggle-all" data-target-id="settings-checkboxes">Select All</span>
                                <span class="curpm-accordion-icon">&#9660;</span>
                            </div>
                            <div class="curpm-accordion-content" id="module-settings">
                                <div class="curpm-permission-group" id="settings-checkboxes">
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-general" class="curpm-checkbox"><span class="curpm-permission-title">General Config</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-seo" class="curpm-checkbox"><span class="curpm-permission-title">SEO Config</span>
                                    </label>
                                    <label class="curpm-checkbox-wrapper">
                                        <input type="checkbox" name="permissions[]" value="setting-roles" class="curpm-checkbox" checked><span class="curpm-permission-title">Manage Roles</span>
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
            // Stop propagation to prevent accordion header click event from firing
            // (Note: event.stopPropagation() is handled inline in HTML to ensure it fires first)
            
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

        // --- 3. Update Toggle Button Text on initial load and checkbox click ---
        function curpm_updateToggleText(containerId, button) {
            const checkboxes = document.querySelectorAll(`#${containerId} .curpm-checkbox`);
            if (checkboxes.length === 0 || !button) return;

            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            button.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
        
        // --- 4. Main Initialization and Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            // a) Initialize Accordion Headers
            document.querySelectorAll('.curpm-accordion-header').forEach(header => {
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
                    e.stopPropagation(); // Prevent the parent accordion header from closing
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


            // d) Form Submission Handler
            document.getElementById('curpm-role-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const roleName = document.getElementById('curpm-role-name').value;
                const roleSlug = document.getElementById('curpm-role-slug').value;
                const roleDescription = document.getElementById('curpm-role-description').value;

                // Simple validation
                if (!roleName || !roleSlug) {
                    curpm_showMessage(
                        'Validation Error!', 
                        'Role Name and Role Slug are required fields.', 
                        'error'
                    );
                    return;
                }

                // Collect permissions
                const permissions = Array.from(document.querySelectorAll('.curpm-checkbox:checked'))
                                             .map(cb => cb.value);
                
                // Prepare message for display
                const messageHtml = `
                    <p><strong>Role Name:</strong> ${roleName}</p>
                    <p><strong>Role Slug:</strong> ${roleSlug}</p>
                    <p><strong>Description:</strong> ${roleDescription || 'N/A'}</p>
                    <p><strong>Total Permissions Assigned:</strong> ${permissions.length}</p>
                    <p><strong>Assigned Permissions:</strong></p>
                    <ul>
                        ${permissions.map(p => `<li>${p}</li>`).join('')}
                    </ul>
                `;
                
                curpm_showMessage(
                    'Role Data Captured Successfully!', 
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

</x-admin-layout>