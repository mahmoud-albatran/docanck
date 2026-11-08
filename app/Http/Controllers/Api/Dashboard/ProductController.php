<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        // dd($request->type);
        $query = Product::with('user', 'category', 'subcategory');

        //  Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%")
                  ->orWhereHas('user', function($queryStore) use ($search){
                        $queryStore->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($queryCategory) use ($search){
                        $queryCategory->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subcategory', function($querySubCategory) use ($search){
                        $querySubCategory->where('name', 'like', "%{$search}%");
                  });
            });
        }


        // Sorting
        if ($request->sort_field) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_order == 1 ? 'asc' : 'desc'
            );
        }else{
            $query->inRandomOrder();
        }


            // filter type
        if ($request->filled('typeProduct')) {
                $query->where('type', $request->typeProduct);
        }

        if ($request->filled('type')) {
            $query->whereHas('category', function($queryCategory) use ($request){
                $queryCategory->where('name', '=', $request->type);
            });
            // $query->whereHas('type', $request->type);
        }
        if ($request->filled('typeSub')) {
            $query->whereHas('subcategory', function($querySubCategory) use ($request){
                $querySubCategory->where('name', '=', $request->typeSub);
            });
            // $query->whereHas('type', $request->type);
        }

        // Price Filter
        if ($request->filled('price_min') || $request->filled('price_max')) {

            $min = $request->price_min ?? 0;
            $max = $request->price_max ?? 999999999;

            $query->whereBetween('price', [$min, $max]);
        }


        $perPage = $request->input('per_page', 8);
        $products = $query->paginate($perPage);


        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        //
        $request->validate([
            'name' => ['required'],
            'description' => ['required'],
            'price' => ['required'],
            'image' => ['nullable'],
            'user_id' => ['required'],
            'category_id' => ['required'],
            'sub_category_id' => ['required'],
            'type' => ['nullable'],
            'count' => ['required'],
        ]);


        if($request->hasFile('image')){
            $path = $request->file('image')->store('/products', [
                "disk" => "s3",
                // "visibility" => "public"

                ]);
            $image = Storage::disk('s3')->url($path);

            // $image = $request->file('image')->store('/products');
            Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $image,
                'user_id' => $request->user_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'count' => $request->count,
                'type' => $request->type,
                'Wholesale_price' => $request->Wholesale_price,
            ]);

            return response()->json([
                'message' => 'تم انشاء المنتج بنجاح'
            ], 201);
        }
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'count' => $request->count,
            'type' => $request->type,
            'Wholesale_price' => $request->Wholesale_price,
        ]);

        return response()->json([
            'message' => 'تم انشاء المنتج بنجاح'
        ], 201);

        // return response()->json([
        //     'message' => 'انت لا ترسل صورة'
        // ], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $product = Product::findOrFail($id);

        return response()->json([
            'status' => true,
            'product' => $product
        ], 201);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        //
        $request->validate([
            'name' => ['required'],
            'description' => ['required'],
            'price' => ['required'],
            'image' => ['nullable'],
            'user_id' => ['required'],
            'category_id' => ['required'],
            'sub_category_id' => ['required'],
            'type' => ['nullable'],
            'count' => ['required'],
        ]);


        if($request->hasFile('image')){
            $path = $request->file('image')->store('/products', [
                "disk" => "s3",
                // "visibility" => "public"

                ]);
            $image = Storage::disk('s3')->url($path);

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $image,
                'user_id' => $request->user_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'count' => $request->count,
                'type' => $request->type,
                'Wholesale_price' => $request->Wholesale_price,
            ]);

            return response()->json([
                'message' => 'تم تعديل المنتج بنجاح'
            ], 201);
        }
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'count' => $request->count,
            'type' => $request->type,
            'Wholesale_price' => $request->Wholesale_price,
        ]);

        return response()->json([
            'message' => 'تم تعديل المنتج بنجاح'
        ], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        //
        $product->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح'
        ]);
    }


    public function getSelect(Request $request){

        $categoryList = Category::get();
        $storeList = User::where('role', 'store')->get();
        if($request->category_id){
            // dd($request->all());
            $subCategories = SubCategory::where('category_id', $request->category_id)->get();
            return response()->json([
                'categoryList' => $categoryList,
                'storeList' => $storeList,
                'subCategories' => $subCategories,
            ], 201);
        }

        return response()->json([
            'categoryList' => $categoryList,
            'storeList' => $storeList,
        ], 201);
    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        Product::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }





}
