<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/user/lists",
    *   tags={"User"},
    *   summary="Fetch all Users",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $user_data = Users::all();
        return response()->json($user_data);
    }

    /**
     * @OA\Post(
     *     path="/api/user/create",
     *     summary="Create new User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "staff_id"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="staff_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="User created successfully"),
     *             @OA\Property(
     *                 property="new_user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="staff_id", type="integer")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'staff_id' => 'required|exists:staff,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $user = new Users();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->staff_id = $request->staff_id;
        $user->save();

        return response()->json([
            'status' => 'User created successfully',
            'new_user' => $user->only(['name', 'email', 'staff_id']),
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/user/update",
    *     summary="Update User",
    *     tags={"User"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "name", "email", "staff_id"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", example="Updated Name"),
    *             @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
    *             @OA\Property(property="password", type="string", format="password", example="newpassword", nullable=true),
    *             @OA\Property(property="staff_id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_user", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="User not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_user", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_user_data = Users::select('id', 'name', 'email', 'staff_id')->get();
        $user = Users::find($request->id);

        if ($user != null) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'sometimes|string|min:8',
                'staff_id' => 'required|exists:staff,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->messages(),
                    'status_code' => 422
                ], 422);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->staff_id = $request->staff_id;
            $user->save();

            return response()->json([
                'status' => 'Update Complete',
                'updated_user' => $user->only(['name', 'email', 'staff_id']),
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'User not found',
                'status_code' => 500,
                'all_current_user' => $all_user_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/user/delete",
    *     summary="Delete a user",
    *     tags={"User"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_user",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="email", type="string"),
    *                 @OA\Property(property="staff_id", type="integer")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="User not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="User not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_user",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="staff_id", type="integer")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $user = Users::find($request->id);
        $all_user = Users::select('id', 'name', 'email', 'staff_id');
        if ($user != null) {
            $deleted_user = $user->only(['id', 'name', 'email', 'staff_id']);
            $user->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_user' => $deleted_user
            ]);
        } else {
            return response()->json([
                'status' => 'User not found',
                'status_code' => 500,
                'all_current_user' => $all_user
            ]);
        }
    }
}
