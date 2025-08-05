<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Error;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Chhaysok Visal Fake Mart API",
 *     description="Swagger documentation for Fake Mart API"
 * )
 */

class BranchController extends Controller
{

    /**
    * @OA\Get(
    *   path="/api/branch/lists",
    *   tags={"Branch"},
    *   summary="Fetch all Branch",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function index() {
        return Branch::all();
    }
    // Select
        function lists(Request $request){
        // Eloquent/ORM Model -> Database Table - slower more operations, cooler
        $branch_data = Branch::all(); // Way easier to remember

        // Direct SQL Builder DB:: - Faster
        // $branch_data = DB::table('branch')->select('*')->get();
        return response()->json($branch_data);
    }

    // Create
    /**
     * @OA\Post(
     *     path="/api/branch/create",
     *     summary="Create new Branch",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "location", "contact_number"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Main Branch"),
     *             @OA\Property(property="location", type="string", example="Phnom Penh"),
     *             @OA\Property(property="contact_number", type="string", example="555-1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Branch created successfully"),
     *             @OA\Property(
     *                 property="new_branch",
     *                 type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="contact_number", type="string")
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
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */

    function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'           => 'required|unique:branch|max:255',
            'location'       => 'required',
            'contact_number' => 'required',
        ]);
        // dd($validator->errors());
        // foreach ($validator->errors()->messages() as $error){
        //     dd($error);
        // }
        if ($validator->fails()) {
            return response()->json([
                'status'      => 'error',
                'errors'      => $validator->messages(),
                'status_code' => 422
            ], 422);
        }
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->location = $request->location;
        $branch->contact_number = $request->contact_number;
        if ($branch != null) {
            $branch->save();
            return response()->json([
                'status' => 'Branch created successfully',
                'new_branch' => $branch,
                'status_code' => 200
            ]);
        }
    }

    // Update
    /**
    * @OA\Post(
    *     path="/api/branch/update",
    *     summary="Update Branch",
    *     tags={"Branch"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "name", "location", "contact_number"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", maxLength=255, example="Updated Branch"),
    *             @OA\Property(property="location", type="string", example="123 Updated St"),
    *             @OA\Property(property="contact_number", type="string", example="555-1234")
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Branch updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_branch", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Branch not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Branch not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_branch", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    function update(Request $request){
        // $branch_all_data = DB::table('branch')->select('name', 'location', 'contact_number')->get();
        // Eloquent
        $branch_all_data = Branch::select('id','name','location','contact_number')->get();
        $branch = Branch::find($request->id);

        if ($branch != null){
            $branch->name = $request->name;
            $branch->location = $request->location;
            $branch->contact_number = $request->contact_number;
            $branch->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_branch' => $branch,
                'status_code' => 200
            ]);
        }
        else {
            return response()->json([
                'status' => 'Branch not found',
                'status_code' => 500,
                'all_current_branch' => $branch_all_data
            ]);
        }
    }

    // Delete
    /**
    * @OA\Post(
    *     path="/api/branch/delete",
    *     summary="Delete a branch",
    *     tags={"Branch"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Branch deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_branch",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="name", type="string"),
    *                 @OA\Property(property="location", type="string"),
    *                 @OA\Property(property="contact_number", type="string")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Branch not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Branch not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_branch",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="location", type="string"),
    *                     @OA\Property(property="contact_number", type="string")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    function delete(Request $request) {
        $branch = Branch::find($request->id);
        $all_branch = Branch::select('id', 'name', 'location', 'contact_number');
        if ($branch != null) {
            $deleted_branch = $branch->only(['id','name','location','contact_number']);
            $branch->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_branch' => $deleted_branch
            ]);
        }
        else {
            return response()->json([
                'status' => 'Branch not found',
                'status_code' => 500,
                'all_current_branch' => $all_branch
            ]);
        }
    }
}
