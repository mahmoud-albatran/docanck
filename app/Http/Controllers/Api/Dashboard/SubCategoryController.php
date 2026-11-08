<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubCategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = SubCategory::with('category');

        //  Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function($queryCategory) use ($search){
                        $queryCategory->where('name', 'like', "%{$search}%");
                  });
            });
        }


        // Sorting
        if ($request->sort_field) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_order == 1 ? 'asc' : 'desc'
            );
        }

        if ($request->filled('type')) {
            $query->WhereHas('category', function($queryCategory) use ($request){
                $queryCategory->where('name', '=', $request->type);
            });

            // $query->whereHas('type', $request->type);
        }

        $perPage = $request->input('per_page', 8);
        $subCategory = $query->paginate($perPage);


        return response()->json([
            'subCategory' => $subCategory,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->authorize('create', SubCategory::class);
        $request->validate([
            'name' => ['required'],
            'category_id' => ['required']
        ]);


        $subCategory = SubCategory::create($request->all());

        return response()->json([
            'message' => 'تم انشاء التصنيف الفرعي بنجاح'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        //

        $this->authorize('update', $subCategory);
          $request->validate([
            'name' => ['required'],
            'category_id' => ['required']
        ]);


        // $subCategory = SubCategory::findOrFail($id);
        $subCategory->update($request->all());

        return response()->json([
            'message' => 'تم تعديل التصنيف الفرعي بنجاح'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        //
        $this->authorize('delete', $subCategory);
        // $subCategory = SubCategory::findOrFail($id);
        $subCategory->delete();
        return response()->json([
            'message' => 'تم حذف التصنيف الفرعي بنجاح'
        ]);
    }


    public function getSelect(){
        $categoryList = Category::get();

        return response()->json([
            'categoryList' => $categoryList,
        ], 201);
    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        SubCategory::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
