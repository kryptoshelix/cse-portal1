<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        // Role & status are decided exclusively server-side. Least privilege:
        $role = UserRole::from($request->registrableRole());

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'), // hashed via cast
        ]);

        // Privileged columns set explicitly (not fillable):
        $user->forceFill([
            'role' => $role,
            'status' => AccountStatus::Pending,
            'can_review_achievements' => false,
        ])->save();

        if ($role === UserRole::Student) {
            Student::create([
                'user_id' => $user->id,
                'roll_number' => $request->input('roll_number'),
                'program' => $request->input('program', 'B.Tech CSE'),
                'batch' => $request->input('batch'),
            ]);
        } else {
            Faculty::create([
                'user_id' => $user->id,
                'employee_id' => $request->input('employee_id'),
                'designation' => $request->input('designation', 'Assistant Professor'),
                'specialization' => $request->input('specialization'),
            ]);
        }

        return redirect()->route('login')->with('status', 'Registration received. An administrator must approve your account before you can access the portal.');
    }

    public function pendingNotice()
    {
        return view('auth.pending');
    }
}
