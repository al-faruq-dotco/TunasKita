<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareGuide;
use App\Models\GuideCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareGuideController extends Controller
{
    public function index(Request $request)
    {
        $guides = CareGuide::query()
            ->with(['category:id,name', 'author:id,name'])
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->q . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('guide_category_id', $request->category))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.guides.index', [
            'guides'     => $guides,
            'categories' => GuideCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('admin.guides.create', [
            'guide'      => null,
            'categories' => GuideCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        unset($data['image'], $data['remove_image']);

        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('guides', 'public');
        }

        CareGuide::create($data);

        return redirect()->route('admin.guides.index')->with('success', 'Panduan ditambahkan.');
    }

    public function edit(CareGuide $guide)
    {
        return view('admin.guides.edit', [
            'guide'      => $guide,
            'categories' => GuideCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, CareGuide $guide)
    {
        $data = $request->validate($this->rules());
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($guide);
            $data['image'] = $request->file('image')->store('guides', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($guide);
            $data['image'] = null;
        }

        $guide->update($data);

        return redirect()->route('admin.guides.index')->with('success', 'Panduan diperbarui.');
    }

    public function destroy(CareGuide $guide)
    {
        $this->deleteImage($guide);
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('success', 'Panduan dihapus.');
    }

    private function deleteImage(CareGuide $guide): void
    {
        if ($guide->image) {
            Storage::disk('public')->delete($guide->image);
        }
    }

    private function rules(): array
    {
        return [
            'guide_category_id' => ['nullable', 'exists:guide_categories,id'],
            'title'             => ['required', 'string', 'max:150'],
            // The column is TEXT (64 KB), so the limit leaves room for multibyte characters.
            'content'           => ['required', 'string', 'max:15000'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image'      => ['nullable', 'boolean'],
        ];
    }
}