<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Fetch all the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('roles:id,name')->orderBy('id', 'DESC')->paginate(10);

        return response()->json(['users' => $users]);
    }

    /**
     * Store the new user details.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        $request['uuid'] = Str::uuid();
        $user = User::create($request->all());
        $user->assignRole($request->role);

        return response()->json([
            'status' => 'success',
            'message' => "User created successfully.",
            'user_details' => $user,
        ], 201);
    }

    /**
     * Display the details of the given user id.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'status' => 'not_found',
                'message' => "User with the id #{$id} not found.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User found.',
            'user_details' => $user,
        ], 200);
    }

    /**
     * Update the details of the given user id.
     *
     * @param  integer  $id
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, UserRequest $request)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'status' => 'not_found',
                'message' => "User with the id #{$id} not found.",
            ], 404);
        }

        $user->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => "User updated successfully.",
            'user_details' => $user->fresh(),
        ], 201);
    }

    /**
     * Delete the user of the given user id.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'status' => 'not_found',
                'message' => "User with the id #{$id} not found.",
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => "User deleted successfully.",
        ], 201);
    }
}
