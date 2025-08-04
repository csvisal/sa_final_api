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

class BranchController extends Controller
{
    // Select
    function lists(Request $request){
        // Eloquent/ORM Model -> Database Table - slower more operations, cooler
        $branch_data = Branch::all(); // Way easier to remember

        // Direct SQL Builder DB:: - Faster
        // $branch_data = DB::table('branch')->select('*')->get();
        return response()->json($branch_data);
    }

    // Insert
    function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'           => 'required|unique:post|max:255',
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
        if ($branch != null){
            $branch->save();
            return response()->json([
                'status' => 'Branch created successfully',
                'new_branch' => $branch,
                'status_code' => 200
            ]);
        }
    }

    // Update
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
