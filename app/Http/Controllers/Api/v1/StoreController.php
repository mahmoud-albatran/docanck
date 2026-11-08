<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'store_name' => ['required'],
            'description' => ['required'],
            'location' => ['required'],
            'location_desc' => ['required'],
            'phone_number' => ['required'],
            'category_id' => ['required'],
        ]);

        $user = Auth::user();

        $data = $request->all();
        $data['role'] = 'store';

        $user->update($data);

        if($request->subCategories){
            $user->syncSubCategories($request->subCategories);
        }

        return response()->json([
            'message' => 'تم انشاء متجرة ... بانتظار الموافقة من قبل ادارة دكانك'
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
