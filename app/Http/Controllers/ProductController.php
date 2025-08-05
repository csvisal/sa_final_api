<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
    * @OA\Get(
    *   path="/api/product/lists",
    *   tags={"Product"},
    *   summary="Fetch all Products",
    *   @OA\Response(
    *       response=200,
    *       description="Successful operation"
    *   )
    * )
    */
    public function lists(Request $request) {
        $product_data = Product::all();
        return response()->json($product_data);
    }

    /**
     * @OA\Post(
     *     path="/api/product/create",
     *     summary="Create new Product",
     *     tags={"Product"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_name", "cost", "price", "category_id"},
     *             @OA\Property(property="product_name", type="string", maxLength=255, example="Smartphone"),
     *             @OA\Property(property="cost", type="number", format="float", example=200.50),
     *             @OA\Property(property="price", type="number", format="float", example=299.99),
     *             @OA\Property(property="image", type="string", example="product.jpg"),
     *             @OA\Property(property="description", type="string", example="Latest smartphone model"),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Product created successfully"),
     *             @OA\Property(
     *                 property="new_product",
     *                 type="object",
     *                 @OA\Property(property="product_name", type="string"),
     *                 @OA\Property(property="cost", type="number", format="float"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="image", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="category_id", type="integer")
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
     *                     property="product_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The product_name field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     )
     * )
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:255',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable',
            'category_id' => 'required|exists:category,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages(),
                'status_code' => 422
            ], 422);
        }

        $product = new Product();
        $product->product_name = $request->product_name;
        $product->cost = $request->cost;
        $product->price = $request->price;
        $product->image = $request->image;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->save();

        return response()->json([
            'status' => 'Product created successfully',
            'new_product' => $product,
            'status_code' => 200
        ]);
    }

    /**
    * @OA\Post(
    *     path="/api/product/update",
    *     summary="Update Product",
    *     tags={"Product"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id", "product_name", "cost", "price", "category_id"},
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="product_name", type="string", maxLength=255, example="Updated Smartphone"),
    *             @OA\Property(property="cost", type="number", format="float", example=250.50),
    *             @OA\Property(property="price", type="number", format="float", example=349.99),
    *             @OA\Property(property="image", type="string", example="updated_product.jpg"),
    *             @OA\Property(property="description", type="string", example="Updated description"),
    *             @OA\Property(property="category_id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Update Complete"),
    *             @OA\Property(property="updated_product", type="object"),
    *             @OA\Property(property="status_code", type="integer", example=200)
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Product not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Product not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(property="all_current_product", type="array", @OA\Items(type="object"))
    *         )
    *     )
    * )
    */
    public function update(Request $request) {
        $all_product_data = Product::select('id', 'product_name', 'cost', 'price', 'image', 'description', 'category_id')->get();
        $product = Product::find($request->id);

        if ($product != null) {
            $product->product_name = $request->product_name;
            $product->cost = $request->cost;
            $product->price = $request->price;
            $product->image = $request->image;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->save();
            return response()->json([
                'status' => 'Update Complete',
                'updated_product' => $product,
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'status' => 'Product not found',
                'status_code' => 500,
                'all_current_product' => $all_product_data
            ]);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/product/delete",
    *     summary="Delete a product",
    *     tags={"Product"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"id"},
    *             @OA\Property(property="id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Product deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Delete Complete"),
    *             @OA\Property(property="status_code", type="integer", example=200),
    *             @OA\Property(
    *                 property="deleted_product",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="product_name", type="string"),
    *                 @OA\Property(property="cost", type="number", format="float"),
    *                 @OA\Property(property="price", type="number", format="float"),
    *                 @OA\Property(property="image", type="string"),
    *                 @OA\Property(property="description", type="string"),
    *                 @OA\Property(property="category_id", type="integer")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Product not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="Product not found"),
    *             @OA\Property(property="status_code", type="integer", example=500),
    *             @OA\Property(
    *                 property="all_current_product",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="product_name", type="string"),
    *                     @OA\Property(property="cost", type="number", format="float"),
    *                     @OA\Property(property="price", type="number", format="float"),
    *                     @OA\Property(property="image", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="category_id", type="integer")
    *                 )
    *             )
    *         )
    *     )
    * )
    */
    public function delete(Request $request) {
        $product = Product::find($request->id);
        $all_product = Product::select('id', 'product_name', 'cost', 'price', 'image', 'description', 'category_id');
        if ($product != null) {
            $deleted_product = $product->only(['id', 'product_name', 'cost', 'price', 'image', 'description', 'category_id']);
            $product->delete();
            return response()->json([
                'status' => 'Delete Complete',
                'status_code' => 200,
                'deleted_product' => $deleted_product
            ]);
        } else {
            return response()->json([
                'status' => 'Product not found',
                'status_code' => 500,
                'all_current_product' => $all_product
            ]);
        }
    }
}
