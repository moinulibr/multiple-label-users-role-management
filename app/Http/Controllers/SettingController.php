<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /**
     * Show the application settings form.
     */
    public function index()
    {
        // Fetch all settings keyed by their key
        $settings = Setting::all()->keyBy('key')->map->value;

        // Pass settings data to the view
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request)
    {
        // Validation rules for all general, email, and advanced settings
        $request->validate([
            // General & Branding
            'site_name' => 'required|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'timezone' => 'required|string|max:255',
            'pagination_limit' => 'nullable|integer|min:5|max:100', // New Field
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512', // New Field (Favicon)

            // Email/SMTP
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',

            // SEO & Advanced
            'meta_description' => 'nullable|string|max:500', // New Field
            'header_script' => 'nullable|string', // New Field (e.g., for analytics code)
            'maintenance_mode' => 'nullable|string', // For checkbox/switch
        ]);

        // Settings data to be saved (excluding file inputs)
        $settingsData = $request->only(
            'site_name',
            'site_email',
            'timezone',
            'pagination_limit',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'meta_description',
            'header_script'
        );

        // Handle Maintenance Mode Checkbox (stores '1' or '0')
        $settingsData['maintenance_mode'] = $request->has('maintenance_mode') ? '1' : '0';


        // --- File Upload Handling ---

        // Helper function for file uploads
        $uploadFile = function ($request, $requestKey, $settingKey, $directory) {
            if ($request->hasFile($requestKey)) {
                // Delete old file
                $old_file = Setting::getSetting($settingKey);
                if ($old_file && Storage::disk('public')->exists($old_file)) {
                    Storage::disk('public')->delete($old_file);
                }

                // Upload new file
                $path = $request->file($requestKey)->store($directory, 'public');
                return $path;
            }
            return null;
        };

        // Site Logo Upload
        if ($logoPath = $uploadFile($request,'site_logo', 'site_logo', 'settings/logo')) {
            $settingsData['site_logo'] = $logoPath;
        }

        // Favicon Upload
        if ($faviconPath = $uploadFile($request,'favicon', 'favicon', 'settings/favicon')) {
            $settingsData['favicon'] = $faviconPath;
        }

        // Loop through and update/create each setting key-value pair
        foreach ($settingsData as $key => $value) {
            // Note: Password field will be saved even if empty, allowing the user to clear it.
            // If you want to skip empty passwords, add: if ($key == 'smtp_password' && empty($value)) { continue; }
            Setting::setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'System settings updated successfully!');
    }
}
