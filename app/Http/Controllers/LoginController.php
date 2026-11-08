<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerfication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    //



    public function index(Request $request){
        return $request->user()->tokens;
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'unique:users'],
            'password' => ['required', Password::defaults()],
        ]);


        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);



        if ($user->login_code_expires_at && $user->login_code_expires_at->isFuture()) {
            return response()->json([
                'message' => 'رمز التحقق تم إرساله مسبقًا، يرجى الانتظار'
            ], 429);
        }

        if($user){
            $loginCode = rand(111111, 999999);
            $user->update([
                'login_code' => $loginCode,
                'login_code_expires_at' => now()->addMinutes(5),
            ]);



        $response = Http::get('https://tweetsms.ps/api.php', [
            'comm'    => 'sendsms',
            'api_key'=> env('TWEETSMS_API_KEY'),
            'to'      => $request->phone_number,
            'message' => 'كود التحقق هو '.$loginCode,
            'sender'  => env('TWEETSMS_SENDER'),
        ]);

        $result = $response->body();



            // $user->notify(new LoginNeedsVerfication($loginCode));
            return response()->json(['message' => 'تم إرسال رمز التحقق إلى رقم الهاتف.']);


        }else{
            return response()->json([
                'message' => 'بيانات اعتماد غير صالحة',
           ], 401);

        }
    }


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

        // if ($user->login_code_expires_at && $user->login_code_expires_at->isFuture()) {
        //     return response()->json([
        //         'message' => 'رمز التحقق تم إرساله مسبقًا، يرجى الانتظار'
        //     ], 429);
        // }

        $loginCode = random_int(100000, 999999);

        $user->update([
            'login_code' => $loginCode,
            'login_code_expires_at' => now()->addMinutes(5),
        ]);


        $response = Http::get('https://tweetsms.ps/api.php', [
            'comm'    => 'sendsms',
            'api_key'=> env('TWEETSMS_API_KEY'),
            'to'      => $request->phone_number,
            'message' => 'كود التحقق هو '.$loginCode,
            'sender'  => env('TWEETSMS_SENDER'),
        ]);

        $result = $response->body();

        // $user->notify(new LoginNeedsVerfication($loginCode));

        return response()->json([
            'message' => 'The verification code has been sent to your personal email.'
        ]);
    }

    public function verifiy(Request $request){

        // validate the incoming request
        $request->validate([
            // 'email' => 'required|email',
            'phone_number' => ['required'],
            'login_code' => 'required| numeric| between:111111,999999'
        ]);


        // find the user
        $user = User::where('phone_number', $request->phone_number)
        ->where('login_code', $request->login_code)
        ->where('login_code_expires_at', '>=', now())
        ->first();

        if (!$user) {
            return response()->json([
                'message' => 'رمز التحقق غير صحيح أو منتهي'
            ], 401);
        }

        $user->update([
            'login_code' => null,
            'login_code_expires_at' => null,
            'email_verified_at' => now(),
        ]);

        return $user->createToken('login-token')->plainTextToken;


        // if not, return back a message
        return response()->json(['message' => 'رمز التحقق غير صالح.'], 401);
    }




    public function updateprofile(Request $request){
        $request->validate([
            'name' => ['required'],
            'email' => ['nullable'],
            'phone_number' => ['nullable'],
        ]);

        $user = Auth::user();
        $data = $request->all();

        $user->update($data);
        return response()->json([
            'message' => 'تم تعديل الملف الشخصي بنجاح',
            'user' => $user,
        ], 201);

    }

    public function updatepass(Request $request){
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required'],
            'confirm_password' => ['required'],
        ]);


        $user = Auth::user();
        if(Hash::check($request->old_password, $user->password)){
            if($request->new_password == $request->confirm_password){
                // dd($request->all());
                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);
                return response()->json([
                    'message' => 'تم تعديل كلمة المرور بنجاح',
                    'user' => $user,
                ], 201);
            }else{

                return response()->json(['message' => 'كلمة المرور الجديدة غير مطابقة لتأكيد كلمة المرور '], 401);
            }
        }else{
            return response()->json(['message' => 'كلمة المرور الحالية غير مطابقة لكلمة المرور في قاعدة البيانات'], 401);
        }


    }






    public function updatephoto(Request $request){

        $request->validate([
            'profile_photo_path' => ['required']
        ]);

        $user = Auth::user();
        $data = $request->all();


        if($request->hasFile('profile_photo_path')){

            //
            $profile = $request->file('profile_photo_path');
            $uniqueNameprofile = time(). '-'. Str::random(10). '-'. $profile->getClientOriginalExtension();

            $profile->move('storage/clients/profile', $uniqueNameprofile);
            $data['profile_photo_path'] = 'clients/profile/'. $uniqueNameprofile;


            $user->update($data);

            return response()->json([
                'message' => 'تم تعديل صورة الملف الشخصي بنجاح',
                'user' => $user,
            ], 201);
        }
    }






    public function destroy(Request $request){

        // $user = Auth::guard('sanctum')->user();

        // logout from current device
        // $user->currentAccessToken()->delete();
        //logout from single device
        // $user->tokens()->findOrFail($id)->delete();

        // logout from all devices!
        // $user->tokens()->delete();

        $request->user()->currentAccessToken()->delete();


        return response()->json([
            'message' => 'تم تسجيل الخروح',
        ]);
    }











    // notifications /////////////////////////



    public function notifies(){
        $user = Auth::user();
        $notifications =  Auth::user()->notifications()->paginate();

        return response()->json([
            'notifications' => $notifications,
        ], 201);

    }


    public function notificationsread(){
        $user = Auth::user();
        $user->notifications()->unread()->update([
            'read_at' => Carbon::now()
        ]);
        return response()->json([
            'message' => 'تم جعل جميع الرسائلك مقروئة',
        ]);

    }

    public function notificationscount(){
        return Auth::user()->notifications()->unread()->count();
    }


    public function deviceToken(Request $request){
        $user = Auth::user();
        // dd($user);
        if(!$user->deviceTokens()->where('token', $request->token)->exists()){

            $user->deviceTokens()->create([
                'token' => $request->token,
                'device_name' => $request->device_name,
            ]);
        }
        return response()->json([
            'message' => 'موجود بالفعل هذا التوكن'
        ]);
    }


    public function sendSMS($phone, $message)
    {
        // رابط الـ API الخاص بـ TweetSMS
        $url = "https://www.tweetsms.ps/api.php";

        $response = Http::get($url, [
            'comm'     => 'sendsms',
            'user'     => env('TWEET_SMS_USERNAME'),
            'pass'     => env('TWEET_SMS_PASSWORD'),
            'to'       => $phone, // مثال: 9665xxxxxxxx
            'message'  => $message,
            'sender'   => env('TWEET_SMS_SENDER'),
            'unicode'  => 'u', // لدعم اللغة العربية
        ]);

        if ($response->successful()) {
            return "تم الإرسال بنجاح!";
        }

        return "فشل الإرسال: " . $response->body();
    }


}
