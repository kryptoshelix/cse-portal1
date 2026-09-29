<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Faculty;
use App\Models\GalleryItem;
use App\Models\News;
use App\Models\Project;
use App\Models\Publication;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            'news' => News::published()->latest('pinned')->latest('published_at')->take(4)->get(),
            'achievements' => Achievement::published()->latest('achievement_date')->take(6)->with('category', 'owner')->get(),
            'projects' => Project::published()->orderByDesc('featured')->latest('published_at')->take(3)->get(),
            'activities' => Activity::published()->latest('start_date')->take(4)->get(),
            'faculty' => Faculty::with('user')->latest()->take(4)->get(),
            'publications' => Publication::published()->latest('publication_date')->take(4)->get(),
            'gallery' => GalleryItem::published()->latest('taken_on')->take(6)->get(),
            'stats' => [
                'faculty' => Faculty::count(),
                'students' => \App\Models\Student::count(),
                'achievements' => Achievement::published()->count(),
                'publications' => Publication::published()->count(),
            ],
        ]);
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function submitContact(\App\Http\Requests\Admin\ContactMessageRequest $request)
    {
        \App\Models\ContactMessage::create($request->validated());

        return back()->with('status', 'Thank you — your message has been received by the department office.');
    }
}
