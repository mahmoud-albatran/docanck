<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Category::with('subcategory');

        //  Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }


        // Sorting
        if ($request->sort_field) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_order == 1 ? 'asc' : 'desc'
            );
        }

        $perPage = $request->input('per_page', 8);
        $categories = $query->paginate($perPage);


        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $this->authorize('create', Category::class);

         $request->validate([
            'name' => ['required', 'min:3'],
            'icon' => ['required', 'image'],
            'description' => ['required'],
        ]);

        if($request->hasFile('icon')){
            // $image = $request->file('icon')->store('/categoryIcon');
            $path = $request->file('icon')->store('/categoryIcon', [
                "disk" => "s3",
                // "visibility" => "public"

            ]);
            $image = Storage::disk('s3')->url($path);
            $slugName = Str::slug($request->name);
            Category::create([
                'name' => $request->name,
                'slug' => $slugName,
                'description' => $request->description,
                'icon' => $image,
            ]);

            return response()->json([
                'message' => 'category created successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'الرجاء اضافة ايقونة للتصنيف'
        ], 400);
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
    public function update(Request $request, Category $category)
    {
        //

        $this->authorize('update', $category);

         $request->validate([
            'name' => ['required', 'min:3'],
            'icon' => ['required', 'image'],
            'description' => ['required'],
        ]);

        if($request->hasFile('icon')){
            // $image = $request->file('icon')->store('/categoryIcon');
            $path = $request->file('icon')->store('/categoryIcon', [
                "disk" => "s3",
                // "visibility" => "public"

            ]);
            $image = Storage::disk('s3')->url($path);
            $slugName = Str::slug($request->name);
            // $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'slug' => $slugName,
                'description' => $request->description,
                'icon' => $image,
            ]);

            return response()->json([
                'message' => 'category updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'الرجاء اضافة ايقونة للتصنيف'
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
        $this->authorize('delete', $category);

        // $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'The category was successfully deleted'
        ]);
    }


    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        Category::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
