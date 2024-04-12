<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest()->get();
        $userName = Auth::user()->name; 
        $email = Auth::user()->email; // Retrieve the authenticated user's email
    
    return view('index', compact('products', 'userName', 'email'));
    }
    /**
     * Display the products view with borders.
     *
     * @return \Illuminate\Contracts\View\View
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {  
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validator->errors(),
            ], 422);    
        }

        $product = Product::create($request->all());

        $response = [
            'status' => 'success',
            'message' => 'Product is added successfully.',
            'data' => $product,
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product is not found!',
            ], 404);
        }

        $response = [
            'status' => 'success',
            'message' => 'Product is retrieved successfully.',
            'data' => $product,
        ];
        
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {  
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validator->errors(),
            ], 422);
        }

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product is not found!',
            ], 404);
        }

        $product->update($request->all());
        
        $response = [
            'status' => 'success',
            'message' => 'Product is updated successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product is not found!',
            ], 404);
        }

        $product->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Product is deleted successfully.'
        ], 200);
    }

    /**
     * Search by a product name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $products = Product::where('name', 'like', '%'.$name.'%')
            ->latest()->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'No product found!',
            ], 404);
        }

        $response = [
            'status' => 'success',
            'message' => 'Products are retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
        
    }
}
