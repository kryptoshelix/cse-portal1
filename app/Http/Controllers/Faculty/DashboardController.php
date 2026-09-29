<?php

namespace App\Http\Controllers\Faculty;

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

        // Faculty explicitly granted review duties see their pending queue size.
        $reviewQueue = $user->canReviewAchievements()
            ? Achievement::where('status', AchievementStatus::Pending->value)->count()
            : null;

        return view('faculty.dashboard', compact('counts', 'recent', 'reviewQueue'));
    }
}
