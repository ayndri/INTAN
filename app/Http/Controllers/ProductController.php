<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        $productCount = $product->count();
        $mostQuantity = $product->max('stock');
        $mostProduct = Product::orderBy('stock', 'desc')->first()->name;
        $lessQuantity = $product->min('stock');
        $lessProduct = Product::orderBy('stock', 'asc')->first()->name;
        $brand = Brand::all();
        $unit = Unit::all();

        return view('content.product.index', [
            'totalProduct' => $productCount,
            'mostQuantity' => $mostQuantity,
            'mostProduct' => $mostProduct,
            'lessQuantity' => $lessQuantity,
            'lessProduct' => $lessProduct,
            'unit' => $unit,
            'brand' => $brand
        ]);
    }

    public function list(Request $request)
    {
        // Define the columns to map the request's order to the database fields
        $columns = [
            1 => 'id',
            2 => 'name',
            3 => 'sku',        // Replacing 'description' with 'sku' (unique product identifier)
            4 => 'stock',      // Current stock level
            5 => 'price',      // Selling price
            6 => 'cost'        // Cost price (additional column for the cost)
        ];

        $search = [];

        // Get the total count of product items
        $totalData = Product::count();
        $totalFiltered = $totalData;

        // Get the pagination, order, and search information from the request
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            // If no search value is provided, get all items with pagination and ordering
            $productItems = Product::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            // If search value is provided, filter the results
            $search = $request->input('search.value');

            $productItems = Product::where('id', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('sku', 'LIKE', "%{$search}%") // Replacing 'description' with 'sku'
                ->orWhere('stock', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->orWhere('cost', 'LIKE', "%{$search}%") // Added 'cost' for search
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            // Count the filtered results
            $totalFiltered = Product::where('id', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('sku', 'LIKE', "%{$search}%") // Replacing 'description' with 'sku'
                ->orWhere('stock', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->orWhere('cost', 'LIKE', "%{$search}%") // Added 'cost' for filtering
                ->count();
        }

        $data = [];

        if (!empty($productItems)) {
            // Provide a dummy ID for display purposes
            $ids = $start;

            foreach ($productItems as $item) {
                $nestedData['id'] = $item->id;
                $nestedData['fake_id'] = ++$ids;
                $nestedData['name'] = $item->name;
                $nestedData['sku'] = $item->sku; // Replacing 'description' with 'sku'
                $nestedData['stock'] = $item->stock; // Stock value
                $nestedData['price'] = $item->price; // Product selling price
                $nestedData['cost'] = $item->cost; // Adding cost

                // Determine if the product_image is a full URL or a relative path
                if (filter_var($item->product_image, FILTER_VALIDATE_URL)) {
                    $nestedData['product_image'] = $item->product_image; // Use it directly if it's a full URL
                } else {
                    $nestedData['product_image'] = asset('storage/' . $item->product_image); // Use storage path if it's a relative path
                }

                $data[] = $nestedData;
            }
        }

        if ($data) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'code' => 200,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'Internal Server Error',
                'code' => 500,
                'data' => [],
            ]);
        }
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',    // Validation for selling price
            'cost' => 'required|numeric|min:0',     // Validation for cost price
            'stock' => 'required|integer|min:0',    // Validation for stock
            'brand_id' => 'nullable|exists:brands,id', // Validate that brand_id exists in the brands table
            'unit_id' => 'nullable|exists:units,id',  // Validate that unit_id exists in the units table
            'status' => 'required|boolean',         // Validate status as true or false
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192', // Validation for the image
        ]);

        // Check if SKU is provided; if not, generate a unique SKU
        if (!$request->has('sku') || empty($request->sku)) {
            do {
                $sku = 'SKU' . rand(1000, 9999);
            } while (Product::where('sku', $sku)->exists());
        } else {
            $sku = 'SKU' . $request->sku;
        }

        $productID = $request->id;
        $imagePath = null;

        if ($productID) {
            // Find the existing product
            $product = Product::find($productID);

            // Handle the product image upload
            if ($request->hasFile('product_image')) {
                // Delete the old image if it exists
                if ($product && $product->product_image && Storage::disk('public')->exists($product->product_image)) {
                    Storage::disk('public')->delete($product->product_image);
                }

                // Store the new image
                $imagePath = $request->file('product_image')->store('product_images', 'public');
            } else {
                // Keep the old image path if a new image is not uploaded
                $imagePath = $product->product_image;
            }

            // Update existing product
            $product->update([
                'name' => $request->name,
                'sku' => $sku,
                'price' => $request->input('price'),
                'cost' => $request->input('cost'),
                'stock' => $request->stock,
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id,
                'status' => $request->status,
                'product_image' => $imagePath,
            ]);

            return response()->json('Updated');
        } else {
            // Create a new product item
            if ($request->hasFile('product_image')) {
                $imagePath = $request->file('product_image')->store('product_images', 'public');
            }

            Product::create([
                'name' => $request->name,
                'sku' => $sku,
                'price' => $request->input('price'),
                'cost' => $request->input('cost'),
                'stock' => $request->stock,
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id,
                'status' => $request->status,
                'product_image' => $imagePath,
            ]);

            return response()->json('Created');
        }
    }


    public function edit($id)
    {
        $where = ['id' => $id];

        $product = Product::where($where)->first();

        return response()->json($product);
    }

    public function destroy($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if ($product) {
            // Check if the product has an image
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                // Delete the image from storage
                Storage::disk('public')->delete($product->product_image);
            }

            // Delete the product record from the database
            $product->delete();

            return response()->json(['message' => 'Product and image deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
