<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
  public function index()
  {
    $product = Product::all();
    $productCount = $product->count();
    $brand = Brand::all();
    $unit = Unit::all();

    return view('content.product.index', [
      'totalProduct' => $productCount,
      'unit' => $unit,
      'brand' => $brand
    ]);
  }

  public function listLowStock(Request $request)
  {
    return $this->getFilteredProducts($request, 'low');
  }

  public function listOutStock(Request $request)
  {
    return $this->getFilteredProducts($request, 'out');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'sku',
      4 => 'category_id', // Maps to the category name
      5 => 'brand_id',    // Maps to the brand name
      6 => 'sell_price',  // Product price
      7 => 'unit_id',     // Maps to the unit name
      8 => 'quantity'     // Current stock level
    ];

    $search = [];
    $totalData = Product::count();
    $totalFiltered = $totalData;

    // Get pagination, order, and search information from the request
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      // Fetch all items with pagination and ordering
      $productItems = Product::with(['category', 'brand', 'unit', 'images'])
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      // Filter results based on search value
      $search = $request->input('search.value');
      $productItems = Product::with(['category', 'brand', 'unit', 'images'])
        ->where('name', 'LIKE', "%{$search}%")
        ->orWhere('sku', 'LIKE', "%{$search}%")
        ->orWhereHas('category', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhereHas('brand', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhere('sell_price', 'LIKE', "%{$search}%")
        ->orWhereHas('unit', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhere('quantity', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = Product::with(['category', 'brand', 'unit', 'images'])
        ->where('name', 'LIKE', "%{$search}%")
        ->orWhere('sku', 'LIKE', "%{$search}%")
        ->orWhereHas('category', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhereHas('brand', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhere('sell_price', 'LIKE', "%{$search}%")
        ->orWhereHas('unit', function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhere('quantity', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];
    if (!empty($productItems)) {
      $ids = $start;
      foreach ($productItems as $item) {
        $nestedData['id'] = $item->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $item->name;
        $nestedData['sku'] = $item->sku;
        $nestedData['category'] = $item->category->name ?? 'N/A'; // Category name
        $nestedData['brand'] = $item->brand->brand_name ?? 'N/A';       // Brand name
        $nestedData['price'] = $item->sell_price;                // Selling price
        $nestedData['unit'] = $item->unit->unit_name ?? 'N/A';        // Unit name
        $nestedData['quantity'] = $item->quantity;               // Quantity in stock

        // Include the first image URL if it exists
        $nestedData['product_image'] = asset('storage/' . $item->images->first()->image ?? null);

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

  public function getFilteredProducts(Request $request, $filter)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'sku',
      4 => 'category_id', // Maps to the category name
      5 => 'brand_id',    // Maps to the brand name
      6 => 'sell_price',  // Product price
      7 => 'unit_id',     // Maps to the unit name
      8 => 'quantity'     // Current stock level
    ];

    $totalData = Product::where('quantity', '<', 15)->count();
    $totalFiltered = $totalData;

    // Get pagination, order, and search information from the request
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      // Fetch low-stock items with pagination and ordering
      $productItems = Product::with(['category', 'brand', 'unit', 'images'])
        ->when($filter === 'low', function ($query) {
          $query->where('quantity', '>', 0)->where('quantity', '<=', 15);
        })
        ->when($filter === 'out', function ($query) {
          $query->where('quantity', '=', 0);
        })
        ->where('quantity', '<', 15)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      // Filter results based on search value
      $search = $request->input('search.value');
      $productItems = Product::with(['category', 'brand', 'unit', 'images'])
        ->when($filter === 'low', function ($query) {
          $query->where('quantity', '>', 0)->where('quantity', '<=', 15);
        })
        ->when($filter === 'out', function ($query) {
          $query->where('quantity', '=', 0);
        })
        ->where('quantity', '<', 15)
        ->where(function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%")
            ->orWhere('sku', 'LIKE', "%{$search}%")
            ->orWhereHas('category', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('brand', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhere('sell_price', 'LIKE', "%{$search}%")
            ->orWhereHas('unit', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            });
        })
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = Product::with(['category', 'brand', 'unit', 'images'])
        ->when($filter === 'low', function ($query) {
          $query->where('quantity', '>', 0)->where('quantity', '<=', 15);
        })
        ->when($filter === 'out', function ($query) {
          $query->where('quantity', '=', 0);
        })
        ->where('quantity', '<', 15)
        ->where(function ($query) use ($search) {
          $query->where('name', 'LIKE', "%{$search}%")
            ->orWhere('sku', 'LIKE', "%{$search}%")
            ->orWhereHas('category', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('brand', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhere('sell_price', 'LIKE', "%{$search}%")
            ->orWhereHas('unit', function ($q) use ($search) {
              $q->where('name', 'LIKE', "%{$search}%");
            });
        })
        ->count();
    }

    $data = [];
    if (!empty($productItems)) {
      $ids = $start;
      foreach ($productItems as $item) {
        $nestedData['id'] = $item->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $item->name;
        $nestedData['sku'] = $item->sku;
        $nestedData['category'] = $item->category->name ?? 'N/A'; // Category name
        $nestedData['brand'] = $item->brand->brand_name ?? 'N/A'; // Brand name
        $nestedData['price'] = $item->sell_price;                // Selling price
        $nestedData['unit'] = $item->unit->unit_name ?? 'N/A';   // Unit name
        $nestedData['quantity'] = $item->quantity;               // Quantity in stock

        // Include the first image URL if it exists
        $nestedData['product_image'] = asset('storage/' . $item->images->first()->image ?? null);

        $data[] = $nestedData;
      }
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
  }

  public function editForm($id)
  {
    $brand = Brand::where('status', true)->get();
    $unit = Unit::where('status', true)->get();
    $category = Category::where('status', true)->get();
    $product = Product::with(['images', 'variantProducts', 'category', 'unit', 'brand'])->findOrFail($id);

    return view('content.product.edit', [
      'brands' => $brand,
      'units' => $unit,
      'categories' => $category,
      'product' => $product
    ]);
  }

  public function store(Request $request)
  {
    // Validate the incoming request
    $request->validate([
      'name' => 'required|string|max:255',
      'sku' => 'nullable|string|max:100|unique:products,sku,' . $request->id,
      'category_id' => 'nullable|exists:categories,id',
      'brand_id' => 'nullable|exists:brands,id',
      'unit_id' => 'nullable|exists:units,id',
      'item_code' => 'nullable|string|max:100|unique:products,item_code,' . $request->id,
      'description' => 'nullable|string',
      'product_type' => 'required|string',
      'sell_price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'quantity_alert' => 'required|integer|min:0',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192', // Allow multiple images
      'variants.*.value' => 'nullable|string',
      'variants.*.price' => 'nullable|numeric|min:0',
      'variants.*.quantity' => 'nullable|integer|min:0',
    ]);

    // Generate SKU and Item Code if not provided
    $sku = $request->sku ?? $this->generateUniqueSku();
    $itemCode = $request->item_code ?? $this->generateUniqueItemCode();

    // Handle create or update
    $productID = $request->id;

    if ($productID) {
      // Update existing product
      $product = Product::findOrFail($productID);

      $product->update([
        'name' => $request->name,
        'sku' => $sku,
        'category_id' => $request->category_id,
        'brand_id' => $request->brand_id,
        'unit_id' => $request->unit_id,
        'item_code' => $itemCode,
        'description' => $request->description,
        'product_type' => $request->product_type,
        'sell_price' => $request->sell_price,
        'quantity' => $request->quantity,
        'quantity_alert' => $request->quantity_alert,
      ]);

      // Handle product images
      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          // Check if the product already has images
          foreach ($product->images as $existingImage) {
            if ($existingImage->image && Storage::disk('public')->exists($existingImage->image)) {
              // Delete the existing image from storage
              Storage::disk('public')->delete($existingImage->image);
            }
            // Delete the record from the database
            $existingImage->delete();
          }

          // Save the new image to storage
          $imagePath = $image->store('product_images', 'public');

          // Create a new record in the product_images table
          $product->images()->create(['image' => $imagePath]);
        }
      }

      // Handle variants
      if ($request->variants) {
        foreach ($request->variants as $variantData) {
          $product->variantProducts()->updateOrCreate(
            ['value' => $variantData['value']], // Match based on value
            [
              'price' => $variantData['price'],
              'quantity' => $variantData['quantity'],
            ]
          );
        }
      }

      return response()->json('Updated');
    } else {
      // Create new product
      $product = Product::create([
        'name' => $request->name,
        'sku' => $sku,
        'category_id' => $request->category_id,
        'brand_id' => $request->brand_id,
        'unit_id' => $request->unit_id,
        'item_code' => $itemCode,
        'description' => $request->description,
        'product_type' => $request->product_type,
        'sell_price' => $request->sell_price,
        'quantity' => $request->quantity,
        'quantity_alert' => $request->quantity_alert,
      ]);

      // Handle product images
      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $imagePath = $image->store('product_images', 'public');
          $product->images()->create(['image' => $imagePath]);
        }
      }

      // Handle variants
      if ($request->variants) {
        foreach ($request->variants as $variantData) {
          $product->variantProducts()->create([
            'value' => $variantData['value'],
            'price' => $variantData['price'],
            'quantity' => $variantData['quantity'],
          ]);
        }
      }

      return response()->json('Created');
    }
  }

  public function update(Request $request, $id)
  {
    // Validate the incoming request
    $request->validate([
      'name' => 'required|string|max:255',
      'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
      'category_id' => 'nullable|exists:categories,id',
      'brand_id' => 'nullable|exists:brands,id',
      'unit_id' => 'nullable|exists:units,id',
      'item_code' => 'nullable|string|max:100|unique:products,item_code,' . $id,
      'description' => 'nullable|string',
      'product_type' => 'required|string',
      'sell_price' => 'required|numeric|min:0',
      'quantity' => 'required|integer|min:0',
      'quantity_alert' => 'required|integer|min:0',
      'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192', // Allow multiple images
      'variants.*.value' => 'nullable|string',
      'variants.*.price' => 'nullable|numeric|min:0',
      'variants.*.quantity' => 'nullable|integer|min:0',
    ]);

    // Find the product by ID
    $product = Product::with(['images', 'variantProducts'])->findOrFail($id);

    // Update product data
    $product->update([
      'name' => $request->name,
      'sku' => $request->sku ?? $this->generateUniqueSku(),
      'category_id' => $request->category_id,
      'brand_id' => $request->brand_id,
      'unit_id' => $request->unit_id,
      'item_code' => $request->item_code ?? $this->generateUniqueItemCode(),
      'description' => $request->description,
      'product_type' => $request->product_type,
      'sell_price' => $request->sell_price,
      'quantity' => $request->quantity,
      'quantity_alert' => $request->quantity_alert,
    ]);

    // Handle product images
    if ($request->hasFile('images')) {
      // Delete existing images
      foreach ($product->images as $existingImage) {
        if ($existingImage->image && Storage::disk('public')->exists($existingImage->image)) {
          Storage::disk('public')->delete($existingImage->image);
        }
        $existingImage->delete();
      }

      // Save new images
      foreach ($request->file('images') as $image) {
        $imagePath = $image->store('product_images', 'public');
        $product->images()->create(['image' => $imagePath]);
      }
    }

    // Handle variants
    if ($request->variants) {
      // Sync variants: Update or create
      foreach ($request->variants as $variantData) {
        $product->variantProducts()->updateOrCreate(
          ['value' => $variantData['value']], // Match based on value
          [
            'price' => $variantData['price'],
            'quantity' => $variantData['quantity'],
          ]
        );
      }

      // Delete unused variants
      $existingVariantValues = collect($request->variants)->pluck('value')->toArray();
      $product->variantProducts()->whereNotIn('value', $existingVariantValues)->delete();
    }

    return response()->json('Updated');
  }

  public function create(Request $request)
  {
    $brand = Brand::where('status', true)->get();
    $unit = Unit::where('status', true)->get();
    $category = Category::where('status', true)->get();

    return view('content.product.add', [
      'brand' => $brand,
      'unit' => $unit,
      'category' => $category
    ]);
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

  public function lowStock()
  {
    return view('content.product.low-stock');
  }

  public function generateSku()
  {
    $prefix = 'PR';
    $lastSku = DB::table('products')
      ->where('sku', 'like', "{$prefix}%")
      ->orderBy('sku', 'desc')
      ->value('sku');

    if ($lastSku) {
      $lastNumber = (int)substr($lastSku, 2);
      $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Untuk 3 angka
    } else {
      $newNumber = '001'; // Mulai dari 001 jika belum ada SKU
    }

    $newSku = $prefix . $newNumber;

    return response()->json(['sku' => $newSku]);
  }

  public function generateItemCode()
  {
    $prefix = 'IC';
    $lastCode = DB::table('products')
      ->where('item_code', 'like', "{$prefix}%")
      ->orderBy('item_code', 'desc')
      ->value('item_code');

    if ($lastCode) {
      $lastNumber = (int)substr($lastCode, 2);
      $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Untuk 4 angka
    } else {
      $newNumber = '0001'; // Mulai dari 0001 jika belum ada Item Code
    }

    $newItemCode = $prefix . $newNumber;

    return response()->json(['item_code' => $newItemCode]);
  }

  public function search(Request $request)
  {
    $query = $request->input('q');
    $products = Product::where('name', 'LIKE', "%{$query}%")
      ->orWhere('sku', 'LIKE', "%{$query}%")
      ->orWhere('item_code', 'LIKE', "%{$query}%")
      ->get();

    return response()->json($products);
  }
}
