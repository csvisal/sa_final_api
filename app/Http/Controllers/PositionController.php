<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/position/lists",
    *   tags={"Position"},
    *   summary="Fetch all Positions",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $position_data = Position::all();
        return response()->json($position_data);
    }

    /**
     * @OA\Post(
     *     path="/api/position/create",
     *     summary="Create new Position",
     *     tags={"Position"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id", "name"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255, example="Manager"),
     *             @OA\Property(property="description", type="string", example="Store manager position")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Position created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Position created successfully"),
     *             @OA\Property(
     *                 property="new_position",
     *                 type="object",
     *                 @OA\Property(property="branch_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string")
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
     *                     property="branch_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The branch_id field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branch,id',
            'name' => 'required|max:255',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $position = new Position();
        $position->branch_id = $request->branch_id;
        $position->name = $request->name;
        $position->description = $request->description;
        $position->save();

        return response()->json([
            'status' => 'Position created successfully',
            'new_position' => $position,
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/position/update",
    *     summary="Update Position",
    *     tags={"Position"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "branch_id", "name"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="branch_id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", maxLength=255, example="Updated Position"),
    *             @OA\Property(property="description", type="string", example="Updated description")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Position updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_position", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Position not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Position not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_position", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_position_data = Position::select('id','branch_id','name','description')->get();
        $position = Position::find($request->id);

        if ($position != null) {
            $position->branch_id = $request->branch_id;
            $position->name = $request->name;
            $position->description = $request->description;
            $position->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_position' => $position,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'Position not found',
                'status_code' => 500,
                'all_current_position' => $all_position_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/position/delete",
    *     summary="Delete a position",
    *     tags={"Position"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Position deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_position",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="branch_id", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="description", type="string")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Position not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Position not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_position",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="branch_id", type="integer"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="description", type="string")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $position = Position::find($request->id);
        $all_position = Position::select('id', 'branch_id', 'name', 'description');
        if ($position != null) {
            $deleted_position = $position->only(['id','branch_id','name','description']);
            $position->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_position' => $deleted_position
            ]);
        } else {
            return response()->json([
                'status' => 'Position not found',
                'status_code' => 500,
                'all_current_position' => $all_position
            ]);
        }
    }
}
