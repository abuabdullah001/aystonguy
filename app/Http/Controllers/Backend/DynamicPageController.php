<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DynamicPageController extends Controller
{
    public function index(): View
    {
        return view('Backend.settings.dynamic_page');
    }

    public function data(Request $request): JsonResponse
    {
        $pages = DynamicPage::query()->latest();

        return DataTables::eloquent($pages)
            ->addColumn('description_preview', function (DynamicPage $page) {
                return str((string) $page->description)->stripTags()->limit(80)->toString();
            })
            ->addColumn('action', function (DynamicPage $page) {
                return '<div class="d-flex gap-1">'
                    .'<button type="button" class="btn btn-sm btn-primary js-edit-page" data-id="'.$page->id.'">Edit</button>'
                    .'<button type="button" class="btn btn-sm btn-danger js-delete-page" data-id="'.$page->id.'">Delete</button>'
                    .'</div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $page = DynamicPage::query()->create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Dynamic page created successfully.',
            'data' => [
                'id' => $page->id,
            ],
        ]);
    }

    public function edit(DynamicPage $dynamicPage): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $dynamicPage->id,
                'title' => $dynamicPage->title,
                'description' => $dynamicPage->description,
            ],
        ]);
    }

    public function update(Request $request, DynamicPage $dynamicPage): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $dynamicPage->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Dynamic page updated successfully.',
        ]);
    }

    public function destroy(DynamicPage $dynamicPage): JsonResponse
    {
        $dynamicPage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Dynamic page deleted successfully.',
        ]);
    }
}
