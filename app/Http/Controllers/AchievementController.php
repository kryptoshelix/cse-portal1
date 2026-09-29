<?php

namespace App\Http\Controllers;

use App\Enums\AchievementStatus;
use App\Http\Requests\Student\AchievementRequest;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Services\AchievementWorkflowService;
use App\Services\DocumentStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Shared CRUD for the student & faculty achievement portals. The portal is
 * chosen by route prefix (student.* / faculty.*) — both use this controller,
 * scoped strictly to the authenticated user's own records.
 */
class AchievementController extends Controller
{
    public function __construct(
        private AchievementWorkflowService $workflow,
        private DocumentStorageService $documents,
    ) {
    }

    private function portal(Request $request): string
    {
        return str_starts_with($request->route()->getPrefix(), 'faculty') ? 'faculty' : 'student';
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Achievement::class);
        $user = Auth::user();

        $q = Achievement::where('owner_user_id', $user->id)->with('category');

        if ($search = trim((string) $request->query('q'))) {
            $q->where('title', 'like', '%'.addcslashes($search, '%_').'%');
        }
        $status = $request->query('status');
        $q->status($status);

        $items = $q->latest('updated_at')->paginate(10)->withQueryString();

        return view('portal.achievements.index', [
            'items' => $items,
            'categories' => AchievementCategory::orderBy('name')->get(),
            'search' => $request->query('q'),
            'statusFilter' => $status,
            'portal' => $this->portal($request),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Achievement::class);

        return view('portal.achievements.form', [
            'achievement' => new Achievement(['status' => AchievementStatus::Draft]),
            'categories' => AchievementCategory::orderBy('name')->get(),
            'portal' => $this->portal($request),
        ]);
    }

    public function store(AchievementRequest $request)
    {
        $this->authorize('create', Achievement::class);
        $user = Auth::user();

        $achievement = $this->createRecord($request, $user);

        if ($request->input('action') === 'submit') {
            $this->workflow->submit($achievement, $user);

            return redirect()->route($this->portal($request).'.achievements.index')
                ->with('status', 'Achievement submitted for review.');
        }

        return redirect()->route($this->portal($request).'.achievements.show', $achievement)
            ->with('status', 'Draft saved. You can submit it for review when ready.');
    }

    public function show(Request $request, Achievement $achievement)
    {
        $this->authorize('view', $achievement);

        return view('portal.achievements.show', [
            'achievement' => $achievement->load(['category', 'owner', 'creator', 'reviewer', 'documents', 'participants']),
            'portal' => $this->portal($request),
        ]);
    }

    public function edit(Request $request, Achievement $achievement)
    {
        $this->authorize('update', $achievement);

        return view('portal.achievements.form', [
            'achievement' => $achievement,
            'categories' => AchievementCategory::orderBy('name')->get(),
            'portal' => $this->portal($request),
        ]);
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        // Policy: only owner/creator may edit, and only while draft or rejected.
        $this->authorize('update', $achievement);
        $user = Auth::user();

        $achievement->fill($request->only(
            'title', 'description', 'achievement_category_id', 'achievement_date',
            'issuing_organization', 'level', 'public_display_consent'
        ))->save();

        if ($request->hasFile('evidence')) {
            $this->documents->storeEvidence($request->file('evidence'), $achievement, $user);
        }

        if ($request->input('action') === 'submit') {
            $this->workflow->submit($achievement->fresh(), $user);

            return redirect()->route($this->portal($request).'.achievements.index')
                ->with('status', 'Achievement resubmitted for review.');
        }

        return redirect()->route($this->portal($request).'.achievements.show', $achievement)
            ->with('status', 'Changes saved.');
    }

    public function submitForReview(Request $request, Achievement $achievement)
    {
        $this->authorize('submit', $achievement);
        $this->workflow->submit($achievement, Auth::user());

        return back()->with('status', 'Achievement submitted for review.');
    }

    public function destroy(Request $request, Achievement $achievement)
    {
        $this->authorize('delete', $achievement);
        $achievement->delete();

        return redirect()->route($this->portal($request).'.achievements.index')
            ->with('status', 'Draft deleted.');
    }

    private function createRecord(AchievementRequest $request, $user): Achievement
    {
        // Owner/created_by set exclusively server-side from the session.
        $achievement = Achievement::create(array_merge(
            $request->only('title', 'description', 'achievement_category_id', 'achievement_date',
                'issuing_organization', 'level', 'public_display_consent'),
            ['owner_user_id' => $user->id, 'created_by' => $user->id]
        ));

        $achievement->forceFill(['status' => AchievementStatus::Draft])->save();

        if ($request->hasFile('evidence')) {
            $this->documents->storeEvidence($request->file('evidence'), $achievement, $user);
        }

        return $achievement->fresh();
    }
}
