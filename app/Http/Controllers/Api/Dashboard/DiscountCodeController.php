<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiscountCodeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', DiscountCode::class);

        //
        $query = DiscountCode::query();

        //  Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('Percentage', 'like', "%{$search}%");
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
        $discountcodes = $query->paginate($perPage);


        return response()->json([
            'discountcodes' => $discountcodes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->authorize('create', DiscountCode::class);

         $request->validate([
            'code' => ['required'],
            'Percentage' => ['required'],
        ]);


        DiscountCode::create([
            'code' => $request->code,
            'Percentage' => $request->Percentage,
        ]);

        return response()->json([
            'message' => 'تم اضافة الكود بنجاح'
        ], 200);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $discountcodes = DiscountCode::where('id', $id)->first();
        if($discountcodes){
            return response()->json([
                'discountcodes' => $discountcodes
            ], 200);
        }else{
            return response()->json([
                'message' => 'الكود غير صحيح'
            ], 401);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountCode $discountCode)
    {
        $this->authorize('update', $discountCode);

        //
        $request->validate([
            'code' => ['required'],
            'Percentage' => ['required'],
        ]);


        $discountCode->update([
            'code' => $request->code,
            'Percentage' => $request->Percentage,
        ]);

        return response()->json([
            'message' => 'تم تعديل الكود بنجاح'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountCode $discountCode)
    {
        $this->authorize('delete', $discountCode);

        //
        $discountCode->delete();
        return response()->json([
            'message' => 'تم حذف الكود بنجاح'
        ], 200);
    }


    public function examination(Request $request){
        $request->validate([
            'code' => ['required'],
        ]);

        $discountcodes = DiscountCode::where('code', $request->code)->first();
        if($discountcodes){
            return response()->json([
                'discountcodes' => $discountcodes
            ], 200);
        }else{
            return response()->json([
                'message' => 'الكود غير صحيح'
            ], 401);

        }
    }
}
