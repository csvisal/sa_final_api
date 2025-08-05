<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceItemController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/invoice_item/lists",
    *   tags={"InvoiceItem"},
    *   summary="Fetch all Invoice Items",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $invoice_item_data = InvoiceItem::all();
        return response()->json($invoice_item_data);
    }

    /**
     * @OA\Post(
     *     path="/api/invoice_item/create",
     *     summary="Create new Invoice Item",
     *     tags={"InvoiceItem"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"invoice_id", "product_id", "qty", "price"},
     *             @OA\Property(property="invoice_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="qty", type="integer", example=2),
     *             @OA\Property(property="price", type="number", format="float", example=19.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice Item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Invoice Item created successfully"),
     *             @OA\Property(
     *                 property="new_invoice_item",
     *                 type="object",
     *                 @OA\Property(property="invoice_id", type="integer"),
     *                 @OA\Property(property="product_id", type="integer"),
     *                 @OA\Property(property="qty", type="integer"),
     *                 @OA\Property(property="price", type="number", format="float")
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
     *                     property="invoice_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The invoice_id field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoice,id',
            'product_id' => 'required|exists:product,id',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $invoiceItem = new InvoiceItem();
        $invoiceItem->invoice_id = $request->invoice_id;
        $invoiceItem->product_id = $request->product_id;
        $invoiceItem->qty = $request->qty;
        $invoiceItem->price = $request->price;
        $invoiceItem->save();

        return response()->json([
            'status' => 'Invoice Item created successfully',
            'new_invoice_item' => $invoiceItem,
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/invoice_item/update",
    *     summary="Update Invoice Item",
    *     tags={"InvoiceItem"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "invoice_id", "product_id", "qty", "price"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="invoice_id", type="integer", example=1),
    *             @OA\Property(property="product_id", type="integer", example=1),
    *             @OA\Property(property="qty", type="integer", example=3),
    *             @OA\Property(property="price", type="number", format="float", example=24.99)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Invoice Item updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_invoice_item", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Invoice Item not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Invoice Item not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_invoice_item", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_invoice_item_data = InvoiceItem::select('id', 'invoice_id', 'product_id', 'qty', 'price')->get();
        $invoiceItem = InvoiceItem::find($request->id);

        if ($invoiceItem != null) {
            $invoiceItem->invoice_id = $request->invoice_id;
            $invoiceItem->product_id = $request->product_id;
            $invoiceItem->qty = $request->qty;
            $invoiceItem->price = $request->price;
            $invoiceItem->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_invoice_item' => $invoiceItem,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'Invoice Item not found',
                'status_code' => 500,
                'all_current_invoice_item' => $all_invoice_item_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/invoice_item/delete",
    *     summary="Delete an invoice item",
    *     tags={"InvoiceItem"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Invoice Item deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_invoice_item",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="invoice_id", type="integer"),
    *                 @OA\Property(property="product_id", type="integer"),
    *                 @OA\Property(property="qty", type="integer"),
    *                 @OA\Property(property="price", type="number", format="float")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Invoice Item not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Invoice Item not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_invoice_item",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="invoice_id", type="integer"),
    *                     @OA\Property(property="product_id", type="integer"),
    *                     @OA\Property(property="qty", type="integer"),
    *                     @OA\Property(property="price", type="number", format="float")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $invoiceItem = InvoiceItem::find($request->id);
        $all_invoice_item = InvoiceItem::select('id', 'invoice_id', 'product_id', 'qty', 'price');
        if ($invoiceItem != null) {
            $deleted_invoice_item = $invoiceItem->only(['id', 'invoice_id', 'product_id', 'qty', 'price']);
            $invoiceItem->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_invoice_item' => $deleted_invoice_item
            ]);
        } else {
            return response()->json([
                'status' => 'Invoice Item not found',
                'status_code' => 500,
                'all_current_invoice_item' => $all_invoice_item
            ]);
        }
    }
}
