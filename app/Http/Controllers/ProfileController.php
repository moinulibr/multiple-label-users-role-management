<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Str; // Str helper is external URL চেক করার জন্য দরকার

class ProfileController extends Controller
{
    protected FileUploader $uploader;

    public function __construct(FileUploader $uploader)
    {
        // Dependency Injection এর মাধ্যমে FileUploader-কে ইনজেক্ট করা
        $this->uploader = $uploader;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Name and Email Update Logic
        // fill() call handles name and email since they are in $request->validated()
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 2. Profile Picture Handling Logic

        // **গুরুত্বপূর্ণ:** Accessor বাইপাস করে ডাটাবেসে সেভ করা আসল পাথ (Raw Path) অ্যাক্সেস করা হচ্ছে
        $rawProfilePath = $user->getRawOriginal('profile_picture');

        // চেক করা হচ্ছে যে বর্তমান পাথটি লোকাল স্টোরেজের পাথ নাকি কোনো এক্সটার্নাল URL
        $isLocalPath = $rawProfilePath && !Str::startsWith($rawProfilePath, ['http', 'https']);

        $fileData = $request->get('profile_image_base64')
            ?? $request->file('profile_image');
        if ($request->hasFile('profile_picture')) {
            // A. নতুন ইমেজ আপলোড: পুরানোটি ডিলিট করে নতুনটি আপলোড

            if ($isLocalPath) {
                // পুরাতন লোকাল ইমেজ ডিলিট করা হচ্ছে
                $this->uploader->delete($rawProfilePath);
            }

            // নতুন ইমেজ আপলোড করা এবং ডাটাবেসের জন্য রিলেটিভ পাথ সেভ করা
            $newPath = $this->uploader->upload($request->file('profile_picture'), 'users');
            $user->profile_picture = $newPath;
        } elseif ($request->boolean('remove_profile_picture')) {
            // B. ইমেজ মুছে ফেলার অনুরোধ (যদি চেকবক্স চেক করা থাকে)

            if ($isLocalPath) {
                // লোকাল ইমেজ ডিলিট করা হচ্ছে
                $this->uploader->delete($rawProfilePath);
            }

            // ডাটাবেস থেকে পাথ মুছে ফেলা
            $user->profile_picture = null;
        }

        // সমস্ত পরিবর্তন সেভ করা
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // ফাইল ডিলিট করার লজিক: ইউজার ডিলিট করার আগে তার প্রোফাইল পিকচার ডিলিট করা
        $rawProfilePath = $user->getRawOriginal('profile_picture');
        if ($rawProfilePath && !Str::startsWith($rawProfilePath, ['http', 'https'])) {
            $this->uploader->delete($rawProfilePath);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
