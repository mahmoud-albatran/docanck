<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SliderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

        $query = Slider::query();


        $perPage = $request->input('per_page', 8);
        $sliders = $query->paginate($perPage);


        return response()->json([
            'sliders' => $sliders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Slider::class);

        //
        $request->validate([
            'image' => ['required', 'image'],
        ]);

        if($request->hasFile('image')){
            // $image = $request->file('image')->store('/categoryIcon');
            $path = $request->file('image')->store('/sliders', [
                "disk" => "s3",
                // "visibility" => "public"

                ]);
            $image = Storage::disk('s3')->url($path);
            Slider::create([
                'image' => $image,
            ]);

            return response()->json([
                'message' => 'تم انشاء اعلان بنجاح'
            ], 201);
        }

        return response()->json([
            'message' => 'الرجاء اضافة الصورة'
        ], 401);

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
    public function update(Request $request, Slider $slider)
    {
        $this->authorize('update', $slider);

        //
        $request->validate([
            'image' => ['required', 'image'],
        ]);

        if($request->hasFile('image')){
            // $image = $request->file('image')->store('/SliderIcon');
            $path = $request->file('image')->store('/products', [
                "disk" => "s3",
                // "visibility" => "public"

                ]);
            $image = Storage::disk('s3')->url($path);
            // $slider = Slider::FindOrFail($id);
            $slider->update([
                'image' => $image,
            ]);

            return response()->json([
                'message' => 'تم تعديل اعلان بنجاح'
            ], 201);
        }

        return response()->json([
            'message' => 'الرجاء اضافة الصورة'
        ], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        $this->authorize('delete', $slider);

        //
        // $slider = Slider::FindOrFail($id);
        $slider->delete();
        return response()->json([
            'message' => 'تم حذف اعلان بنجاح'
        ], 201);

    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        Slider::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
