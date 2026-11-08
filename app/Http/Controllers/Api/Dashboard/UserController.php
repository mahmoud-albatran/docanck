<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'roleType' => ['required']
        ]);
        //
         $query = User::with('category', 'role', 'products')->withCount('products')->where('role', $request->roleType);

        //  Searching
        if ($request->has('search') && $request->roleType == 'store') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('store_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('location_desc', 'like', "%{$search}%")
                  ->orWhereHas('category', function($queryCategory) use ($search){
                        $queryCategory->where('name', 'like', "%{$search}%");
                  });
            });
        }
        //  Searching
        if ($request->has('search') && $request->roleType == 'client') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");

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
            $query->whereHas('category', function($queryCategory) use ($request){
                $queryCategory->where('name', '=', $request->type);
            });
            // $query->whereHas('type', $request->type);
        }

        $perPage = $request->input('per_page', 8);
        $users = $query->paginate($perPage);


        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        //

        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone_number' => ['nullable', 'unique:users'],
            'password' => ['required'],
            'profile_photo_path' => ['nullable'],
        ]);

        $role = Role::findOrFail($request->get('role_id'));

        if($request->hasFile('profile_photo_path')){
            // $profile_photo_path = $request->file('profile_photo_path')->store('/profile_photo_path');
            $path = $request->file('profile_photo_path')->store('/profile_photo_path', [
                "disk" => "s3",
                // "visibility" => "public"

            ]);
            $profile_photo_path = Storage::disk('s3')->url($path);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' =>  Hash::make($request->password),
                'profile_photo_path' => $profile_photo_path,
                'store_name' => $request->store_name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'location' => $request->location,
                'location_desc' => $request->location_desc,
                // 'role' => $request->store_name? 'store' : 'client',
               'role' => $role
                        ? ($role->name === 'store' ? 'store' : 'Employee')
                        : 'client',
            ]);

            // $user->assignRole($role->name);
            $user->syncRoles([$role->name]);

            return response()->json([
                'message' => 'تم انشاء المستخدم بنجاح'
            ], 201);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' =>  Hash::make($request->password),
            'store_name' => $request->store_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'location' => $request->location,
            'location_desc' => $request->location_desc,
            // 'role' => $request->store_name? 'store' : 'client',
            'role' => $role
                    ? ($role->name === 'store' ? 'store' : 'Employee')
                    : 'client',
        ]);



        // $user->assignRole($role->name);
        $user->syncRoles([$role->name]);
        return response()->json([
            'message' => 'تم انشاء المستخدم بنجاح'
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
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        //

        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone_number' => ['nullable'],
            'password' => ['required'],
            'profile_photo_path' => ['nullable'],
        ]);


        $role = Role::findOrFail($request->get('role_id'));

        if($request->hasFile('profile_photo_path')){
            // $profile_photo_path = $request->file('profile_photo_path')->store('/profile_photo_path');
            $path = $request->file('profile_photo_path')->store('/profile_photo_path', [
                "disk" => "s3",
                // "visibility" => "public"

            ]);
            $profile_photo_path = Storage::disk('s3')->url($path);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' =>  Hash::make($request->password),
                'profile_photo_path' => $profile_photo_path,
                'store_name' => $request->store_name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'location' => $request->location,
                'location_desc' => $request->location_desc,
                'role' => $role
                        ? ($role->name === 'store' ? 'store' : 'Employee')
                        : 'client',

            ]);

            $user->syncRoles([$role->name]);

            return response()->json([
                'message' => 'تم تعديل المستخدم بنجاح'
            ], 201);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'store_name' => $request->store_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'location' => $request->location,
            'location_desc' => $request->location_desc,
            // 'role' => $request->store_name? 'store' : 'client',
            'role' => $role
                    ? ($role->name === 'store' ? 'store' : 'Employee')
                    : 'client',
        ]);

        $user->syncRoles([$role->name]);

        return response()->json([
            'message' => 'تم تعديل المستخدم بنجاح'
        ], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        //
        $user->delete();
        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح'
        ], 201);

    }

    public function getSelect(){
        $categoryList = Category::get();
        $roleList = Role::get();

        return response()->json([
            'categoryList' => $categoryList,
            'roleList' => $roleList,
        ], 201);
    }

        public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            // 'date' => ['required'],
        ], [
            'file.required' => 'ملف CSV مطلوب',
            // 'date.required' => 'التاريخ مطلوب',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        // تجاوز BOM إن وُجد
        $firstLine = fgets($handle);
        if (str_starts_with($firstLine, "\xEF\xBB\xBF")) {
            $firstLine = substr($firstLine, 3);
        }

        // تخطي الترويسة (3 أسطر للموظفين، سطر واحد لغيرهم)

            fgetcsv($handle, 0, ';'); // رؤوس الأعمدة فقط


        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {

                // ترتيب الأعمدة مطابق للـ export
                [
                    $name,
                    $description,
                    $Wholesale_price,
                    $price,
                    $image,
                    $type,
                    $category_id,
                    $sub_category_id
                ] = $row;


                $store = User::where('id', $request->storeIdFile)->first();


                $store->products()->create([
                    'name' => $name,
                    'description' => $description,
                    'Wholesale_price' => $Wholesale_price,
                    'price' => $price,
                    'image' => $image,
                    'type' => $type,
                    'category_id' => $category_id,
                    'sub_category_id' => $sub_category_id,
                ]);





            }

            DB::commit();
            fclose($handle);

            return response()->json(['message' => 'تم الاستيراد بنجاح']);

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function multipleDelete(Request $request){

        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        User::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }


    public function permissions(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'roles' => $user->getRoleNames(), // ['admin', 'editor']
            'permissions' => $user->getAllPermissions()->pluck('name') // ['edit categories', 'delete categories']
        ]);
    }
}
