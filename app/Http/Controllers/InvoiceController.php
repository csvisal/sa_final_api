<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/invoice/lists",
    *   tags={"Invoice"},
    *   summary="Fetch all Invoices",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $invoice_data = Invoice::all();
        return response()->json($invoice_data);
    }

    /**
     * @OA\Post(
     *     path="/api/invoice/create",
     *     summary="Create new Invoice",
     *     tags={"Invoice"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "total"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="total", type="number", format="float", example=99.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Invoice created successfully"),
     *             @OA\Property(
     *                 property="new_invoice",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="total", type="number", format="float")
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
     *                     property="user_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The user_id field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $invoice = new Invoice();
        $invoice->user_id = $request->user_id;
        $invoice->total = $request->total;
        $invoice->save();

        return response()->json([
            'status' => 'Invoice created successfully',
            'new_invoice' => $invoice,
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/invoice/update",
    *     summary="Update Invoice",
    *     tags={"Invoice"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "user_id", "total"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="user_id", type="integer", example=1),
    *             @OA\Property(property="total", type="number", format="float", example=149.99)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Invoice updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_invoice", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Invoice not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Invoice not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_invoice", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_invoice_data = Invoice::select('id', 'user_id', 'total')->get();
        $invoice = Invoice::find($request->id);

        if ($invoice != null) {
            $invoice->user_id = $request->user_id;
            $invoice->total = $request->total;
            $invoice->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_invoice' => $invoice,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'Invoice not found',
                'status_code' => 500,
                'all_current_invoice' => $all_invoice_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/invoice/delete",
    *     summary="Delete an invoice",
    *     tags={"Invoice"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Invoice deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_invoice",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="user_id", type="integer"),
    *                 @OA\Property(property="total", type="number", format="float")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Invoice not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Invoice not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_invoice",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="user_id", type="integer"),
    *                     @OA\Property(property="total", type="number", format="float")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $invoice = Invoice::find($request->id);
        $all_invoice = Invoice::select('id', 'user_id', 'total');
        if ($invoice != null) {
            $deleted_invoice = $invoice->only(['id', 'user_id', 'total']);
            $invoice->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_invoice' => $deleted_invoice
            ]);
        } else {
            return response()->json([
                'status' => 'Invoice not found',
                'status_code' => 500,
                'all_current_invoice' => $all_invoice
            ]);
        }
    }
}
