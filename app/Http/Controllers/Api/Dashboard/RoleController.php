<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //
    // public function __construct()
    // {
    //     $this->authorizeResource(Permission\Models\Role::class , 'role');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $roles = Role::withCount('permissions')->orderBy('id' , 'desc')->paginate(10);
        // // $roles = Role::orderBy('id' , 'desc')->paginate(10);
        // return response()->view('cms.spatie.roles.index', ['roles' => $roles]);

        $roles = Role::where([
            ['name' , '!=' , Null],
            [function ($query) use ($request) {
                if (($name = $request->name)) {
                    $query->orWhere('name' , 'Like' , '%' . $name . '%')
                          ->orwhere('guard_name' , 'Like' , '%' . $name . '%')
                          ->get();
                }
            }]
        ])
        ->withCount('permissions')->orderBy('id' , 'desc')->paginate(10);

        // return response()->view('cms.spatie.roles.index', compact('roles' ));
        return response()->json([
            'roles' => $roles
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

        return response()->view('cms.spatie.roles.create');
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
            $role = new Role();
            $role->name = $request->get('name');
            $role->guard_name = $request->get('guard_name');
            $isSaved = $role->save();
            return response()->json(['message' => $isSaved ? "Created Successfully" : "Failed to create"], $isSaved ? 201 : 400);
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
        $role = Role::findOrFail($id);
        // return response()->view('cms.spatie.roles.edit', ['role' => $role]);
        return response()->json([
            'role' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator($request->all(), [
            'name' => 'required|string|max:100',
            'guard_name' => 'required|string|in:web',
        ]);

        if (!$validator->fails()) {

            $role = Role::findOrFail($id);
            // $role = new Role();
            $role->name = $request->get('name');
            $role->guard_name = $request->get('guard_name');
            $isSave = $role->save();
            return response()->json(['message' => $isSave ? "Created Successfully" : "Failed to create"], $isSave ? 201 : 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        $isDeleted = Role::destroy($id);
        return response()->json(['message' => $isDeleted ? "Deleted successfully" : "Failed to delete"], $isDeleted ? 200 : 400);

}

}
