<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //
     // public function __construct()
    // {
    //     $this->authorizeResource(Permission\Models\Permission::class , 'permission');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $permissions = Permission::all();

        // $permissions = Permission::orderBy('id' , 'desc')->paginate(10);
        // return response()->view('cms.spatie.permissions.index', ['permissions' => $permissions]);
        $permissions = Permission::where([
            ['name' , '!=' , Null],
            [function ($query) use ($request) {
                if (($name = $request->name)) {
                    $query->orWhere('name' , 'Like' , '%' . $name . '%')
                    ->orWhere('guard_name' , 'Like' , '%' . $name . '%')
                    ->paginate();
                }
            }]
        ])
        ->orderBy('id' , 'desc')->paginate(5);

        // return response()->view('cms.spatie.permissions.index' , compact('permissions' ));
        return response()->json([
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return response()->view('cms.spatie.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator($request->all(), [
            'name' => 'required|string|max:100',
            'guard_name' => 'required|string|in:web',
        ]);

        if (!$validator->fails()) {
            $permission = new Permission();
            $permission->name = $request->get('name');
            $permission->guard_name = $request->get('guard_name');
            $isSaved = $permission->save();
            return response()->json(['message' => $isSaved ? "Saved successfully" : "Failed to save"], $isSaved ? 201 : 400);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $permission = Permission::findOrFail($id);
        // return response()->view('cms.spatie.permissions.edit', ['permission' => $permission]);
        return response()->json([
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        $validator = Validator($request->all(), [
            'name' => 'required|string|max:100',
            'guard_name' => 'required|string|in:web',
        ]);

        if (!$validator->fails()) {
            $permission = Permission::FindOrFail($id);
            $permission->name = $request->get('name');
            $permission->guard_name = $request->get('guard_name');
            $isSaved = $permission->save();
            return response()->json(['message' => $isSaved ? "Saved successfully" : "Failed to save"], $isSaved ? 200 : 400);
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $isDeleted = Permission::destroy($id);
        return response()->json(['message' => $isDeleted ? "Deleted successfully" : "Failed to delete"], $isDeleted ? 200 : 400);
    }
}
