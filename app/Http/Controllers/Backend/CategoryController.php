<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('Backend.categories.index');
    }

    public function data(Request $request): JsonResponse
    {
        $categories = Category::query()->latest();

        return DataTables::eloquent($categories)
            ->addColumn('status', function (Category $category) {
                $label = $category->is_active ? 'Active' : 'Inactive';
                $btnClass = $category->is_active ? 'btn-success' : 'btn-secondary';

                return '<button type="button" class="btn btn-sm '.$btnClass.' js-toggle-category-status" data-id="'.$category->id.'">'.$label.'</button>';
            })
            ->addColumn('action', function (Category $category) {
                return '<div class="d-flex gap-1">'
                    .'<button type="button" class="btn btn-sm btn-primary js-edit-category" data-id="'.$category->id.'">Edit</button>'
                    .'<button type="button" class="btn btn-sm btn-danger js-delete-category" data-id="'.$category->id.'">Delete</button>'
                    .'</div>';
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update([
            'is_active' => ! (bool) $category->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category status updated successfully.',
            'data' => [
                'id' => $category->id,
                'is_active' => (bool) $category->is_active,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = Category::query()->create([
            'title' => $validated['title'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully.',
            'data' => [
                'id' => $category->id,
            ],
        ]);
    }

    public function edit(Category $category): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $category->id,
                'title' => $category->title,
                'is_active' => (bool) $category->is_active,
            ],
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'title' => $validated['title'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully.',
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
