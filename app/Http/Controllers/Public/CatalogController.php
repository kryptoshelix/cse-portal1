<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Activity;
use App\Models\Faculty;
use App\Models\GalleryItem;
use App\Models\News;
use App\Models\Patent;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Student;
use Illuminate\Http\Request;

/**
 * Public catalog. SECURITY: every query here is constrained to published
 * records; unpublished content must never be reachable through these routes.
 */
class CatalogController extends Controller
{
    private const PER_PAGE = 12;

    public function achievements(Request $request)
    {
        $q = Achievement::published()->with(['category', 'owner']);
        if ($search = trim((string) $request->query('q'))) {
            $like = '%'.addcslashes($search, '%_').'%';
            $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('issuing_organization', 'like', $like));
        }
        if ($cat = $request->query('category')) {
            $q->whereHas('category', fn ($c) => $c->where('slug', $cat));
        }
        // Respect display consent on public listings.
        $q->where('public_display_consent', true);

        return view('public.achievements', [
            'items' => $q->latest('achievement_date')->paginate(self::PER_PAGE)->withQueryString(),
            'categories' => AchievementCategory::orderBy('name')->get(),
            'search' => $request->query('q'),
        ]);
    }

    public function achievement(string $slugOrId)
    {
        $item = Achievement::published()
            ->where('public_display_consent', true)
            ->where('id', $slugOrId)
            ->with(['category', 'owner.student', 'participants', 'documents' => fn ($d) => $d->where('is_private', false)])
            ->firstOrFail();

        return view('public.achievement-show', ['item' => $item]);
    }

    public function activities(Request $request)
    {
        $items = Activity::published()->when(trim((string) $request->query('q')),
            fn ($q) => $q->where('title', 'like', '%'.$request->query('q').'%'))
            ->latest('start_date')->paginate(self::PER_PAGE)->withQueryString();

        return view('public.activities', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function activity(string $slug)
    {
        return view('public.activity-show', ['item' => Activity::published()->where('slug', $slug)->firstOrFail()]);
    }

    public function projects(Request $request)
    {
        $items = Project::published()->with('guide.user')
            ->when(trim((string) $request->query('q')), fn ($q) => $q->where('title', 'like', '%'.$request->query('q').'%'))
            ->latest('year')->paginate(self::PER_PAGE)->withQueryString();

        return view('public.projects', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function project(int $id)
    {
        return view('public.project-show', ['item' => Project::published()->with('guide.user')->findOrFail($id)]);
    }

    public function publications(Request $request)
    {
        $items = Publication::published()->with('faculty.user')
            ->when(trim((string) $request->query('q')), fn ($q) => $q->where('title', 'like', '%'.$request->query('q').'%'))
            ->latest('publication_date')->paginate(self::PER_PAGE)->withQueryString();

        return view('public.publications', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function patents(Request $request)
    {
        $items = Patent::published()->with('faculty.user')
            ->when(trim((string) $request->query('q')), fn ($q) => $q->where('title', 'like', '%'.$request->query('q').'%'))
            ->latest('filing_date')->paginate(self::PER_PAGE)->withQueryString();

        return view('public.patents', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function facultyIndex(Request $request)
    {
        $items = Faculty::with('user')->when(trim((string) $request->query('q')),
            fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->query('q').'%')))
            ->paginate(9)->withQueryString();

        return view('public.faculty', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function students(Request $request)
    {
        // Privacy policy: only students who opted in are listed publicly.
        $items = Student::with('user')->where('public_display_consent', true)
            ->when(trim((string) $request->query('q')), fn ($q) => $q->where('roll_number', 'like', '%'.$request->query('q').'%'))
            ->paginate(15)->withQueryString();

        return view('public.students', ['items' => $items, 'search' => $request->query('q')]);
    }

    public function gallery(Request $request)
    {
        $items = GalleryItem::published()->latest('taken_on')->paginate(12)->withQueryString();

        return view('public.gallery', ['items' => $items]);
    }

    public function news(Request $request)
    {
        $items = News::published()->latest('pinned')->latest('published_at')
            ->paginate(10)->withQueryString();

        return view('public.news', ['items' => $items]);
    }

    public function newsShow(string $slug)
    {
        return view('public.news-show', ['item' => News::published()->where('slug', $slug)->firstOrFail()]);
    }
}
