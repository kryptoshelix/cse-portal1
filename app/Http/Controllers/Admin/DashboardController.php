<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'pendingSubmissions' => Achievement::where('status', AchievementStatus::Pending->value)->count(),
            'pendingAccounts' => User::where('status', \App\Enums\AccountStatus::Pending->value)->count(),
            'approvedNotPublished' => Achievement::where('status', AchievementStatus::Approved->value)->count(),
            'published' => Achievement::where('status', AchievementStatus::Published->value)->count(),
            'totalStudents' => User::where('role', UserRole::Student->value)->count(),
            'totalFaculty' => User::where('role', UserRole::Faculty->value)->count(),
            'recentActivity' => AuditLog::latest()->take(10)->get(),
            'user' => Auth::user(),
        ]);
    }
}
