<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotBelongsToUser;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Model\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductCollection::collection(Product::paginate(5));
    }


    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return void
     */
    public function store(ProductRequest $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->detail = $request->description;
        $product->stock = $request->stock;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->user_id = auth()->id();
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Model\Product       $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->ProductUserCheck($product);
        $request['detail'] = $request->description;
        unset($request['description']);
        $product->update($request->all());

        return response([
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Model\Product $product
     * @return bool|null
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response([
            'success' => true
        ], Response::HTTP_NO_CONTENT);
    }

    public function ProductUserCheck($product)
    {
        if (Auth::id() !== $product->user_id) {
            throw new ProductNotBelongsToUser("Error processing request.", 1);
        }

    }
}
