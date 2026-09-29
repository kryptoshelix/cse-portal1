<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AchievementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AchievementReviewRequest;
use App\Http\Requests\Admin\StoreAchievementRequest;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Services\AchievementWorkflowService;
use App\Services\AuditLogger;
use App\Services\DocumentStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function __construct(
        private AchievementWorkflowService $workflow,
        private DocumentStorageService $documents,
        private AuditLogger $audit,
    ) {
    }

    public function index(Request $request)
    {
        $q = Achievement::with(['owner', 'category']);

        if ($search = trim((string) $request->query('q'))) {
            $like = '%'.addcslashes($search, '%_').'%';
            $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhereHas('owner', fn ($o) => $o->where('name', 'like', $like)));
        }
        $q->status($request->query('status'));
        if ($cat = $request->query('category')) {
            $q->where('achievement_category_id', $cat);
        }

        return view('admin.achievements.index', [
            'items' => $q->latest('submitted_at')->latest()->paginate(15)->withQueryString(),
            'categories' => AchievementCategory::orderBy('name')->get(),
            'statuses' => AchievementStatus::values(),
        ]);
    }

    public function create()
    {
        return view('admin.achievements.form', [
            'achievement' => new Achievement(),
            'categories' => AchievementCategory::orderBy('name')->get(),
            'owners' => \App\Models\User::whereIn('role', ['student', 'faculty'])->orderBy('name')->get(['id', 'name', 'role']),
        ]);
    }

    public function store(StoreAchievementRequest $request)
    {
        $user = Auth::user();

        $achievement = Achievement::create(array_merge(
            $request->only('title', 'description', 'achievement_category_id', 'achievement_date',
                'issuing_organization', 'level', 'public_display_consent'),
            ['owner_user_id' => $request->input('owner_user_id'), 'created_by' => $user->id]
        ));

        // Admin-created records start as drafts unless explicitly submitted.
        $status = $request->input('action') === 'submit' ? AchievementStatus::Pending : AchievementStatus::Draft;
        $achievement->forceFill([
            'status' => $status,
            'featured' => $request->boolean('featured'),
            'submitted_at' => $status === AchievementStatus::Pending ? now() : null,
        ])->save();

        if ($request->hasFile('evidence')) {
            $this->documents->storeEvidence($request->file('evidence'), $achievement, $user);
        }

        $this->audit->log('achievement.created', "Admin created achievement #{$achievement->id}", $achievement);

        return redirect()->route('admin.achievements.show', $achievement)->with('success', 'Achievement created.');
    }

    public function show(Achievement $achievement)
    {
        return view('admin.achievements.show', [
            'achievement' => $achievement->load(['category', 'owner.student', 'creator', 'reviewer', 'documents', 'participants']),
        ]);
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.form', [
            'achievement' => $achievement,
            'categories' => AchievementCategory::orderBy('name')->get(),
            'owners' => \App\Models\User::whereIn('role', ['student', 'faculty'])->orderBy('name')->get(['id', 'name', 'role']),
        ]);
    }

    public function update(StoreAchievementRequest $request, Achievement $achievement)
    {
        $achievement->fill($request->only('title', 'description', 'achievement_category_id',
            'achievement_date', 'issuing_organization', 'level', 'public_display_consent'));
        $achievement->featured = $request->boolean('featured');

        if ($request->filled('owner_user_id') && (int) $request->input('owner_user_id') !== $achievement->owner_user_id) {
            $achievement->owner_user_id = $request->input('owner_user_id');
        }

        $achievement->save();

        if ($request->hasFile('evidence')) {
            $this->documents->storeEvidence($request->file('evidence'), $achievement, Auth::user());
        }

        $this->audit->log('achievement.updated', "Admin updated achievement #{$achievement->id}", $achievement);

        return redirect()->route('admin.achievements.show', $achievement)->with('success', 'Achievement updated.');
    }

    public function review(AchievementReviewRequest $request, Achievement $achievement)
    {
        $reviewer = Auth::user();

        if ($request->input('decision') === 'approve') {
            $this->workflow->approve($achievement, $reviewer);

            return back()->with('success', 'Submission approved. It can now be published.');
        }

        $this->workflow->reject($achievement, $reviewer, (string) $request->input('rejection_feedback'));

        return back()->with('success', 'Submission rejected and the applicant has been given feedback.');
    }

    public function publish(Achievement $achievement)
    {
        $this->authorize('publish', $achievement);
        $this->workflow->publish($achievement, Auth::user());

        return back()->with('success', 'Achievement is now publicly visible.');
    }

    public function unpublish(Achievement $achievement)
    {
        $this->workflow->unpublish($achievement, Auth::user());

        return back()->with('success', 'Achievement removed from the public site.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        $this->audit->log('achievement.deleted', "Achievement #{$achievement->id} deleted", $achievement);

        return redirect()->route('admin.achievements.index')->with('success', 'Achievement deleted.');
    }
}
