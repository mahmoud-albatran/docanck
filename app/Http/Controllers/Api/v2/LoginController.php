<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //

     public function submit(Request $request)
    {
        $request->validate([
            // 'email' => 'required|email',
            'phone_number' => ['required'],
            'password' => 'required'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (! $user) {
            return response()->json([
                'message' => 'لا يوجد مستخدم بهذا البيانات،الرجاء انشاء حساب جديد'
            ], 401);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'كلمة المرور غير صحيحة'
            ], 401);
        }

        return $user->createToken('login-token')->plainTextToken;


    }


    public function deleteAccount(Request $request){

        // $user = Auth::guard('sanctum')->user();

        // logout from current device
        // $user->currentAccessToken()->delete();
        //logout from single device
        // $user->tokens()->findOrFail($id)->delete();

        // logout from all devices!
        // $user->tokens()->delete();

        $request->user()->delete();


        return response()->json([
            'message' => 'تم حذف الحساب',
        ]);
    }



}
