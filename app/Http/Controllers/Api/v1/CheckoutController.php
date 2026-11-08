<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrder;
use App\Notifications\TrackOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $user = Auth::user();

        if($user->role == 'admin'){

            $query = Order::with('user', 'adress.user', 'orderItems.product.user', 'code')->latest();

                //  Searching
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('total_price', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function($queryClient) use ($search){
                        $queryClient->where('name', 'like', "%{$search}%");
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

            $perPage = $request->input('per_page', 8);
            $orders = $query->paginate($perPage);


            return response()->json([
                'orders' => $orders,
            ]);
        }else{
            $query = $user->orders()->with('user', 'adress', 'orderItems.product');

                //  Searching
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('total_price', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");


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
            $myOrder = $query->paginate($perPage);


            return response()->json([
                'orders' => $myOrder,
            ]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       $request->validate([
            'address_id' => ['required'],
            'total_price' => ['required'],
            'carts' => ['required']
        ],[
            'carts' => 'لا يوجد عناصر بالسلة',
            'address_id' => 'الرجاء ارسال العنوان التوصيل',
            'total_price' => 'اجمالي السعر مطلوب'
        ]);
        $user = $request->user();
        $carts = $request->carts;
        $products = $request->products;

        $mergedData = [];

        $order = Order::create([
            'Price_before_discount' => $request->Price_before_discount,
            'total_price' => $request->total_price,
            'user_id' => $user->id,
            'address_id' => $request->address_id,
            'payment' => $request->payment,
            'code_id' => $request->code_id
        ]);



        foreach ($carts as $cartItem) {

            $order->orderItems()->create([
                'unit_price' =>$cartItem["unit_price"],
                'quantity' =>$cartItem["quantity"],
                'product_id' =>$cartItem["product_id"],
            ]);

        }

        $adminstrator = User::where('role', 'admin')->get();
        // dd($adminstrator);
        $client = User::where('id', $user->id)->first();

        foreach($adminstrator as $userAdmin){
            $userAdmin->notify(new NewOrder($order, $client));
        }


        $admin = User::where('role', 'admin')->first();

        $client->notify(new TrackOrder($order, $admin, 'طلبك قيد الانتظار،نشكرك على تعاملك مع دكانك'));


        return response()->json([
            'message' => 'تم انشاء طلب الرجاء الانتظار'
        ], 201);



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::with('user', 'orderItems', 'adress')->findOrFail($id);


        $client = Auth::user();
        $notifications = $client->notifications()->where('data->order_id', (string) $order->id)->get();

        return response()->json([
            'order' => $order,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

         $request->validate([
            'status' => ['required'],
            'message' => ['required'],

        ],[
            'status' => 'حقل حالة الطلب مطلوب',
            'message' => 'الرجاء ترك رسالة',
        ]);
        $user = $request->user();
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);




        $client = $order->user;




        $admin = Auth::user();

        $client->notify(new TrackOrder($order, $admin, $request->message));


        return response()->json([
            'message' => 'تم تحديث حالة الطلب'
        ], 201);



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json([
            'message' => 'تم حذف بنجاح'
        ]);
    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        Order::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
