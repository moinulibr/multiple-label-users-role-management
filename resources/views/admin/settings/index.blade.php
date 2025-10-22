<x-admin-layout>
<x-slot name="page_title">
Settings
</x-slot>

<!-- Custom CSS for Tabs and Design -->
<style>
    .custom-tab-nav {
        display: flex;
        padding: 0 20px;
        background-color: #f8f9fa; /* Light background for the strip */
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0;
    }

    .custom-tab-button {
        padding: 10px 15px;
        text-decoration: none;
        color: #495057; /* Dark gray text */
        font-weight: 500;
        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: color 0.2s, border-bottom 0.2s;
        margin-right: 20px; /* Spacing between buttons */
        position: relative;
        font-size: 15px;
        text-transform: uppercase;
    }

    .custom-tab-button:hover:not(.active) {
        color: #007bff; /* Primary color on hover */
    }

    .custom-tab-button.active {
        color: #007bff; /* Active color */
        border-bottom: 3px solid #007bff; /* Underline effect for active tab */
    }

    .tab-pane {
        display: none;
        padding: 20px;
        animation: fadeIn 0.3s;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .form-label {
        font-weight: 600 !important;
    }

    .card-header h2 {
        padding: 15px 20px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-default shadow-lg border-0 rounded-lg">
            <div class="card-header border-bottom-0 p-0">
                <h2 class="text-2xl font-semibold text-gray-800">System Configuration</h2>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success mx-4 mt-3" role="alert">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Tab Navigation (Custom Buttons) -->
                    <div class="custom-tab-nav" id="settingsTab">
                        <button type="button" class="custom-tab-button active" data-target-id="general">General</button>
                        <button type="button" class="custom-tab-button" data-target-id="branding">Branding & Media</button>
                        <button type="button" class="custom-tab-button" data-target-id="email">Email (SMTP)</button>
                        <button type="button" class="custom-tab-button" data-target-id="advanced">Advanced & SEO</button>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="settingsTabContent" style="min-height: 400px;">
                        
                        {{-- ------------------- 1. GENERAL TAB ------------------- --}}
                        <div class="tab-pane active" id="general">
                            <h5 class="text-lg font-medium mb-4 text-primary border-bottom pb-2">System Information</h5>

                            <div class="form-group mb-4">
                                <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                <input type="text" id="site_name" name="site_name" class="form-control"  
                                        value="{{ $settings['site_name'] ?? '' }}" required>
                                @error('site_name')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="site_email" class="form-label">Contact Email (Outgoing)</label>
                                <input type="email" id="site_email" name="site_email" class="form-control"  
                                        value="{{ $settings['site_email'] ?? '' }}">
                                @error('site_email')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="timezone" class="form-label">Timezone</label>
                                <input type="text" id="timezone" name="timezone" class="form-control"  
                                        value="{{ $settings['timezone'] ?? 'Asia/Dhaka' }}" required placeholder="e.g., Asia/Dhaka or UTC">
                                <small class="form-text text-muted">Set the default timezone for the application.</small>
                                @error('timezone')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="pagination_limit" class="form-label">Default Pagination Limit</label>
                                <input type="number" id="pagination_limit" name="pagination_limit" class="form-control"  
                                        value="{{ $settings['pagination_limit'] ?? 15 }}" min="5" max="100">
                                <small class="form-text text-muted">Number of items to show per page in data lists.</small>
                                @error('pagination_limit')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- ------------------- 2. BRANDING TAB ------------------- --}}
                        <div class="tab-pane" id="branding">
                            <h5 class="text-lg font-medium mb-4 text-primary border-bottom pb-2">Branding & Visuals</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Site Logo -->
                                    <div class="form-group mb-4">
                                        <label for="site_logo" class="form-label">Site Logo (Recommended: PNG, Max 2MB)</label>
                                        <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                                        @error('site_logo')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                        
                                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                                            <div class="mt-3 p-3 border rounded bg-light d-flex align-items-center">
                                                <strong class="me-3">Current Logo:</strong> 
                                                {{-- Logo display fix --}}
                                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                                                     onerror="this.onerror=null; this.src='https://placehold.co/150x60/333333/FFFFFF?text=Logo+Missing';"
                                                     alt="Site Logo" 
                                                     style="max-height: 80px; border-radius: 4px; border: 1px solid #ccc;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Favicon -->
                                    <div class="form-group mb-4">
                                        <label for="favicon" class="form-label">Favicon (Recommended: ICO/PNG, Max 512KB)</label>
                                        <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,image/png">
                                        @error('favicon')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                        
                                        @if(isset($settings['favicon']) && $settings['favicon'])
                                            <div class="mt-3 p-3 border rounded bg-light d-flex align-items-center">
                                                <strong class="me-3">Current Favicon:</strong> 
                                                <img src="{{ asset('storage/' . $settings['favicon']) }}" 
                                                     onerror="this.onerror=null; this.src='https://placehold.co/40x40/333333/FFFFFF?text=Fav';"
                                                     alt="Favicon" 
                                                     style="max-height: 40px; border-radius: 4px; border: 1px solid #ccc;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ------------------- 3. EMAIL/SMTP TAB ------------------- --}}
                        <div class="tab-pane" id="email">
                            <h5 class="text-lg font-medium mb-4 text-primary border-bottom pb-2">Email Configuration (SMTP)</h5>
                            <p class="text-muted mb-4">These settings are used to send system emails, like password resets and notifications.</p>

                            <div class="form-group mb-4">
                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" id="smtp_host" name="smtp_host" class="form-control"  
                                        value="{{ $settings['smtp_host'] ?? '' }}" placeholder="e.g., smtp.gmail.com">
                                @error('smtp_host')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                <input type="number" id="smtp_port" name="smtp_port" class="form-control"  
                                        value="{{ $settings['smtp_port'] ?? 587 }}" placeholder="e.g., 587 or 465">
                                @error('smtp_port')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="smtp_username" class="form-label">SMTP Username</label>
                                <input type="text" id="smtp_username" name="smtp_username" class="form-control"  
                                        value="{{ $settings['smtp_username'] ?? '' }}" placeholder="Your SMTP username">
                                @error('smtp_username')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                <input type="password" id="smtp_password" name="smtp_password" class="form-control"  
                                        placeholder="Enter new password to change, leave blank to keep current">
                                @error('smtp_password')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- ------------------- 4. ADVANCED TAB ------------------- --}}
                        <div class="tab-pane" id="advanced">
                            <h5 class="text-lg font-medium mb-4 text-primary border-bottom pb-2">SEO & Maintenance</h5>

                            <!-- Meta Description -->
                            <div class="form-group mb-4">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" class="form-control" rows="3" placeholder="A concise description of the site for search engines.">{{ $settings['meta_description'] ?? '' }}</textarea>
                                @error('meta_description')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Header Script -->
                            <div class="form-group mb-4">
                                <label for="header_script" class="form-label">Header Scripts (Tracking, Analytics)</label>
                                <textarea id="header_script" name="header_script" class="form-control font-monospace" rows="5" placeholder="Paste Google Analytics, custom CSS, or other scripts here. Will be injected into <head> tag.">{{ $settings['header_script'] ?? '' }}</textarea>
                                @error('header_script')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            
                            <!-- Maintenance Mode Toggle -->
                            <div class="form-group mb-4">
                                <label for="maintenance_mode" class="form-label">Maintenance Mode</label>
                                <div class="form-check form-switch mt-2">
                                    @php
                                        $isMaintenance = ($settings['maintenance_mode'] ?? '0') === '1';
                                    @endphp
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" @if($isMaintenance) checked @endif>
                                    <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                                </div>
                                <small class="form-text text-danger">When enabled, the site will show a maintenance message.</small>
                            </div>
                        </div>

                    </div>
                    
                    <!-- Submit Button -->
                    <div class="card-footer d-flex justify-content-end bg-light p-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2 text-uppercase fw-bold shadow-sm">Save All Settings</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript for Tab Switching (Guaranteed to work) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select all custom tab buttons and content panes
        const tabButtons = document.querySelectorAll('.custom-tab-button');
        const tabContents = document.querySelectorAll('.tab-pane');

        // Function to handle tab activation
        function setActiveTab(button) {
            const targetId = button.getAttribute('data-target-id');

            // Deactivate all buttons and content
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Activate the clicked button
            button.classList.add('active');

            // Activate the corresponding content pane
            const activeContent = document.getElementById(targetId);
            if (activeContent) {
                activeContent.classList.add('active');
            }
            
            // Optional: Update URL hash for persistence (not critical for functionality)
            window.history.pushState(null, '', window.location.pathname + '?tab=' + targetId);
        }

        // Add click listener to all tab buttons
        tabButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                setActiveTab(this);
            });
        });

        // Handle initial load based on URL or default to first tab
        const urlParams = new URLSearchParams(window.location.search);
        const activeTabId = urlParams.get('tab');
        let initialButton = null;

        if (activeTabId) {
            initialButton = document.querySelector(`.custom-tab-button[data-target-id="${activeTabId}"]`);
        }
        
        // If no tab in URL or button not found, default to the first one
        if (!initialButton && tabButtons.length > 0) {
            initialButton = tabButtons[0];
        }

        if (initialButton) {
            setActiveTab(initialButton);
        }
    });
</script>


</x-admin-layout>