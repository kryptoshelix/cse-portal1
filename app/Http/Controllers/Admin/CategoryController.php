<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\AchievementCategory;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(private AuditLogger $audit)
    {
    }

    public function index(Request $request)
    {
        $items = AchievementCategory::withCount('achievements')
            ->when(trim((string) $request->query('q')), fn ($q) => $q->where('name', 'like', '%'.$request->query('q').'%'))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', ['items' => $items]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new AchievementCategory()]);
    }

    public function store(CategoryRequest $request)
    {
        $category = AchievementCategory::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
        ]);
        $this->audit->log('category.created', "Category #{$category->id} created", $category);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(AchievementCategory $category)
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(CategoryRequest $request, AchievementCategory $category)
    {
        $category->update(['name' => $request->input('name'), 'description' => $request->input('description')]);
        $this->audit->log('category.updated', "Category #{$category->id} updated", $category);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(AchievementCategory $category)
    {
        if ($category->achievements()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete a category that still has achievements.']);
        }
        $category->delete();
        $this->audit->log('category.deleted', "Category #{$category->id} deleted");

        return back()->with('status', 'Category deleted.');
    }
}
