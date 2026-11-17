<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuideCategoryController extends Controller
{
    public function index()
    {
        $categories = GuideCategory::withCount('guides')->orderBy('name')->paginate(10);

        return view('admin.guide-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.guide-categories.create', ['category' => null]);
    }

    public function store(Request $request)
    {
        GuideCategory::create($request->validate($this->rules()));

        return redirect()->route('admin.guide-categories.index')->with('success', 'Kategori panduan ditambahkan.');
    }

    public function edit(GuideCategory $guideCategory)
    {
        return view('admin.guide-categories.edit', ['category' => $guideCategory]);
    }

    public function update(Request $request, GuideCategory $guideCategory)
    {
        $guideCategory->update($request->validate($this->rules($guideCategory->id)));

        return redirect()->route('admin.guide-categories.index')->with('success', 'Kategori panduan diperbarui.');
    }

    public function destroy(GuideCategory $guideCategory)
    {
        if ($guideCategory->guides()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki panduan.');
        }

        $guideCategory->delete();

        return redirect()->route('admin.guide-categories.index')->with('success', 'Kategori panduan dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'        => ['required', 'string', 'max:100', Rule::unique('guide_categories', 'name')->ignore($ignoreId)],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}