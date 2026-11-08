<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAdress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $request->validate([
            'user_id' => ['required'],
        ]);
        $query = UserAdress::with('user')->where('user_id', $request->user_id);

        //  Searching
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('isMain', 'like', "%{$search}%")
                  ->orWhereHas('user', function($queryStore) use ($search){
                        $queryStore->where('name', 'like', "%{$search}%");
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


            // filter type
        if ($request->filled('type')) {
            $query->whereHas('user', function($queryCategory) use ($request){
                $queryCategory->where('name', '=', $request->type);
            });
            // $query->whereHas('type', $request->type);
        }

        $perPage = $request->input('per_page', 8);
        $usersAddress = $query->paginate($perPage);


        return response()->json([
            'address' => $usersAddress,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => ['required'],
            'address' => ['required'],
            'location' => ['nullable'],
            'city' => ['required'],
            'isMain' => ['required'],
        ]);

        $user = Auth::user();
        $user->addresses()->create($request->all());

        return response()->json([
            'message' => 'تم اضافة العنوان بنجاح'
        ], 201);
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
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => ['required'],
            'address' => ['required'],
            'location' => ['nullable'],
            'city' => ['required'],
            'isMain' => ['required'],
        ]);

        $address = UserAdress::FindOrFail($id);
        $address->update($request->all());

        return response()->json([
            'message' => 'تم تعديل العنوان بنجاح'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $address = UserAdress::FindOrFail($id);
        $address->delete();
        return response()->json([
            'message' => 'تم حذف العنوان بنجاح'
        ], 201);

    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        UserAdress::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
