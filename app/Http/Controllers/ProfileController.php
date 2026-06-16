<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
        $validated = $request->validated();

        // Handle boolean cast for checkbox (if the request is from the TTS form, missing means false)
        if ($request->has('is_tts_form')) {
            $validated['tts_enabled'] = isset($validated['tts_enabled']);
            $validated['high_contrast'] = isset($validated['high_contrast']);
            $validated['dyslexia_font'] = isset($validated['dyslexia_font']);
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Toggle a single accessibility setting via AJAX.
     */
    public function toggleAccessibility(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'setting' => ['required', 'string', 'in:high_contrast,dyslexia_font,tts_enabled'],
        ]);

        $user    = $request->user();
        $setting = $request->input('setting');
        $user->$setting = !$user->$setting;
        $user->save();

        return response()->json(['value' => $user->$setting]);
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
