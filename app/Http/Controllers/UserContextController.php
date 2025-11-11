<?php
namespace App\Http\Controllers;

use App\Services\UserContextManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserContextController extends Controller
{
    protected $contextManager;

    public function __construct(UserContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
    }

    /**
     * Switch user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchProfile(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|integer',
        ]);

        $profileId = $request->input('profile_id');
        // set current profile
        $this->contextManager->setCurrentProfile($profileId);

        //after change - just for showing latest value of session
        //$this->contextManager->getAllSessionCacheAndCacheKeys();
        
        return redirect()->intended(route('dashboard'))->with('success', 'Profile Switched successfully।');
    }
}
