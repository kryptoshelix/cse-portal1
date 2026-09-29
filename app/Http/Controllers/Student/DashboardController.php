<?php

namespace App\Http\Controllers\Student;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $counts = Achievement::where('owner_user_id', $user->id)
            ->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        $recent = Achievement::where('owner_user_id', $user->id)
            ->latest('updated_at')->take(6)->with('category')->get();

        $needsAttention = Achievement::where('owner_user_id', $user->id)
            ->where('status', AchievementStatus::Rejected->value)
            ->latest('reviewed_at')->take(3)->get();

        $profile = $user->student;
        $profileFields = [
            'roll_number' => filled($profile?->roll_number),
            'program' => filled($profile?->program),
            'batch' => filled($profile?->batch),
            'phone' => filled($user->phone),
        ];

        return view('student.dashboard', compact('counts', 'recent', 'needsAttention', 'profileFields'));
    }
}
