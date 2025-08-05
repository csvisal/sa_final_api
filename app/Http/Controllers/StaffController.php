<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/staff/lists",
    *   tags={"Staff"},
    *   summary="Fetch all Staff",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $staff_data = Staff::all();
        return response()->json($staff_data);
    }

    /**
     * @OA\Post(
     *     path="/api/staff/create",
     *     summary="Create new Staff",
     *     tags={"Staff"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"position_id", "name", "dob", "pob", "address", "phone", "national_id_card"},
     *             @OA\Property(property="position_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="pob", type="string", example="Phnom Penh"),
     *             @OA\Property(property="address", type="string", example="123 Street, Phnom Penh"),
     *             @OA\Property(property="phone", type="string", example="012345678"),
     *             @OA\Property(property="national_id_card", type="string", example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Staff created successfully"),
     *             @OA\Property(
     *                 property="new_staff",
     *                 type="object",
     *                 @OA\Property(property="position_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="dob", type="string", format="date"),
     *                 @OA\Property(property="pob", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="national_id_card", type="string")
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
     *                     property="position_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The position_id field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'position_id' => 'required|exists:position,id',
            'name' => 'required|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'dob' => 'required|date',
            'pob' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'national_id_card' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $staff = new Staff();
        $staff->position_id = $request->position_id;
        $staff->name = $request->name;
        $staff->gender = $request->gender ?? 'male';
        $staff->dob = $request->dob;
        $staff->pob = $request->pob;
        $staff->address = $request->address;
        $staff->phone = $request->phone;
        $staff->national_id_card = $request->national_id_card;
        $staff->save();

        return response()->json([
            'status' => 'Staff created successfully',
            'new_staff' => $staff,
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/staff/update",
    *     summary="Update Staff",
    *     tags={"Staff"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "position_id", "name", "dob", "pob", "address", "phone", "national_id_card"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="position_id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", maxLength=255, example="Updated Name"),
    *             @OA\Property(property="gender", type="string", example="female"),
    *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
    *             @OA\Property(property="pob", type="string", example="Updated POB"),
    *             @OA\Property(property="address", type="string", example="Updated Address"),
    *             @OA\Property(property="phone", type="string", example="098765432"),
    *             @OA\Property(property="national_id_card", type="string", example="987654321")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Staff updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_staff", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Staff not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Staff not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_staff", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_staff_data = Staff::select('id', 'position_id', 'name', 'gender', 'dob', 'pob', 'address', 'phone', 'national_id_card')->get();
        $staff = Staff::find($request->id);

        if ($staff != null) {
            $staff->position_id = $request->position_id;
            $staff->name = $request->name;
            $staff->gender = $request->gender;
            $staff->dob = $request->dob;
            $staff->pob = $request->pob;
            $staff->address = $request->address;
            $staff->phone = $request->phone;
            $staff->national_id_card = $request->national_id_card;
            $staff->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_staff' => $staff,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'Staff not found',
                'status_code' => 500,
                'all_current_staff' => $all_staff_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/staff/delete",
    *     summary="Delete a staff",
    *     tags={"Staff"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Staff deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_staff",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="position_id", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="gender", type="string"),
    *                 @OA\Property(property="dob", type="string", format="date"),
    *                 @OA\Property(property="pob", type="string"),
    *                 @OA\Property(property="address", type="string"),
    *                 @OA\Property(property="phone", type="string"),
    *                 @OA\Property(property="national_id_card", type="string")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Staff not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Staff not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_staff",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="position_id", type="integer"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="gender", type="string"),
    *                     @OA\Property(property="dob", type="string", format="date"),
    *                     @OA\Property(property="pob", type="string"),
    *                     @OA\Property(property="address", type="string"),
    *                     @OA\Property(property="phone", type="string"),
    *                     @OA\Property(property="national_id_card", type="string")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $staff = Staff::find($request->id);
        $all_staff = Staff::select('id', 'position_id', 'name', 'gender', 'dob', 'pob', 'address', 'phone', 'national_id_card');
        if ($staff != null) {
            $deleted_staff = $staff->only(['id', 'position_id', 'name', 'gender', 'dob', 'pob', 'address', 'phone', 'national_id_card']);
            $staff->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_staff' => $deleted_staff
            ]);
        } else {
            return response()->json([
                'status' => 'Staff not found',
                'status_code' => 500,
                'all_current_staff' => $all_staff
            ]);
        }
    }
}
