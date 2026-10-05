<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $categories = Category::query()
            ->withCount('items')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = \DB::transaction(function () use ($request): Category {
            $category = Category::create($request->validatedData());

            $this->audit->log(
                'category_create',
                'Category',
                $category->id,
                ['name' => $category->name],
                $request->user(),
            );

            return $category;
        });

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        \DB::transaction(function () use ($category, $request): void {
            $category->update($request->validatedData());

            $this->audit->log(
                'category_update',
                'Category',
                $category->id,
                ['name' => $category->name],
                $request->user(),
            );
        });

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category)
    {
        $this->authorize('delete', $category);

        \DB::transaction(function () use ($category, $request): void {
            $this->audit->log(
                'category_delete',
                'Category',
                $category->id,
                ['name' => $category->name],
                $request->user(),
            );

            $category->delete();
        });

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
