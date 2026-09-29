<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();

        return view('portal.profile', [
            'profile' => $user->student ?? $user->facultyProfile,
            'portal' => str_starts_with($request->route()->getPrefix(), 'faculty') ? 'faculty' : 'student',
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        // Only safe personal fields; role/status ids are not accepted here at all.
        $user->fill($request->only('name', 'phone', 'address'))->save();

        if ($student = $user->student) {
            $student->fill($request->only('program', 'batch'))->save();
            // Consent is the student's own decision:
            $student->public_display_consent = $request->boolean('public_display_consent');
            $student->save();
        } elseif ($faculty = $user->facultyProfile) {
            $faculty->fill($request->only('designation', 'specialization', 'qualification', 'bio'))->save();
        }

        return back()->with('status', 'Profile updated.');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->forceFill(['password' => $request->input('new_password')])->save();
        $request->session()->regenerate();

        return back()->with('status', 'Password changed successfully.');
    }
}
