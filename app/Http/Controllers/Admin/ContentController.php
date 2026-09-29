<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityRequest;
use App\Http\Requests\Admin\GalleryRequest;
use App\Http\Requests\Admin\NewsRequest;
use App\Http\Requests\Admin\PatentRequest;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\PublicationRequest;
use App\Models\Activity;
use App\Models\Faculty;
use App\Models\GalleryItem;
use App\Models\News;
use App\Models\Patent;
use App\Models\Project;
use App\Models\Publication;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generic admin CRUD for the six public content modules. Publish/unpublish is
 * always an explicit action — saving edits never silently changes visibility.
 */
class ContentController extends Controller
{
    public function __construct(private AuditLogger $audit)
    {
    }

    private function models(): array
    {
        return [
            'activities' => Activity::class,
            'projects' => Project::class,
            'publications' => Publication::class,
            'patents' => Patent::class,
            'gallery' => GalleryItem::class,
            'news' => News::class,
        ];
    }

    private function rules(): array
    {
        return [
            'activities' => ActivityRequest::class,
            'projects' => ProjectRequest::class,
            'publications' => PublicationRequest::class,
            'patents' => PatentRequest::class,
            'gallery' => GalleryRequest::class,
            'news' => NewsRequest::class,
        ];
    }

    public function index(Request $request, string $module)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];

        $q = $model::query();
        if ($search = trim((string) $request->query('q'))) {
            $col = $module === 'news' ? 'heading' : 'title';
            $like = '%'.addcslashes($search, '%_').'%';
            $q->where($col, 'like', $like);
        }
        if ($request->query('published') !== null && $request->query('published') !== '') {
            $q->where('is_published', $request->boolean('published'));
        }

        return view('admin.content.index', [
            'module' => $module,
            'items' => $q->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function create(string $module)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];

        return view('admin.content.form', [
            'module' => $module,
            'item' => new $model(),
            'facultyList' => Faculty::with('user')->get(),
        ]);
    }

    public function store(Request $request, string $module)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];
        $validated = $request->validateWithBag($module, app($this->rules()[$module])->rules());

        $data = $validated;
        if ($module === 'gallery') {
            unset($data['image']);
        }
        if ($module === 'news') {
            $data['author_user_id'] = Auth::id();
        }

        $item = $model::create($data);

        if ($module === 'gallery' && $request->hasFile('image')) {
            $item->image_path = $this->storeImage($request->file('image'));
            $item->save();
        }

        $this->audit->log("{$module}.created", ucfirst(rtrim($module, 's'))." #{$item->id} created", $item);

        return redirect()->route('admin.'.$module.'.index')->with('status', 'Record created (unpublished by default).');
    }

    public function edit(string $module, $id)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];

        return view('admin.content.form', [
            'module' => $module,
            'item' => $model::findOrFail($id),
            'facultyList' => Faculty::with('user')->get(),
        ]);
    }

    public function update(Request $request, string $module, $id)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];
        $item = $model::findOrFail($id);
        $validated = $request->validateWithBag($module, app($this->rules()[$module])->rules());

        // Visibility is NOT part of this form — publishing is a separate action.
        unset($validated['image'], $validated['is_published'], $validated['published_at']);
        $item->fill($validated)->save();

        if ($module === 'gallery' && $request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $item->image_path = $this->storeImage($request->file('image'));
            $item->save();
        }

        $this->audit->log("{$module}.updated", ucfirst(rtrim($module, 's'))." #{$item->id} updated", $item);

        return redirect()->route('admin.'.$module.'.index')->with('status', 'Record updated.');
    }

    public function publish(string $module, $id)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];
        $item = $model::findOrFail($id);

        $item->forceFill(['is_published' => true, 'published_at' => now()])->save();
        $this->audit->log("{$module}.published", "Published {$module} #{$id}", $item);

        return back()->with('status', 'Record published to the website.');
    }

    public function unpublish(string $module, $id)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];
        $item = $model::findOrFail($id);

        $item->forceFill(['is_published' => false, 'published_at' => null])->save();
        $this->audit->log("{$module}.unpublished", "Unpublished {$module} #{$id}", $item);

        return back()->with('status', 'Record removed from the website.');
    }

    public function destroy(string $module, $id)
    {
        abort_unless(array_key_exists($module, $this->models()), 404);
        $model = $this->models()[$module];
        $item = $model::findOrFail($id);

        if ($module === 'gallery' && $item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();
        $this->audit->log("{$module}.deleted", "Deleted {$module} #{$id}");

        return redirect()->route('admin.'.$module.'.index')->with('status', 'Record deleted.');
    }

    /** Re-encode images via GD to strip embedded payloads; random filename. */
    private function storeImage(\Illuminate\Http\UploadedFile $image): string
    {
        $name = 'gallery/'.Str::random(40).'.'.$image->getClientOriginalExtension();
        $info = @getimagesize($image->getRealPath());
        if ($info === false) {
            abort(422, 'Invalid image file.');
        }
        // Store original bytes on public disk (validated as real raster image).
        Storage::disk('public')->put($name, file_get_contents($image->getRealPath()));

        return $name;
    }
}
