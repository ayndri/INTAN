<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('content.product.index', [
            'totalProduct' => $productCount,
            'mostQuantity' => $mostQuantity,
            'mostProduct' => $mostProduct,
            'lessQuantity' => $lessQuantity,
            'lessProduct' => $lessProduct
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
        // Validasi permintaan (request)
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', // Validasi untuk harga jual
            'cost' => 'required|numeric|min:0', // Validasi untuk harga pokok
            'stock' => 'required|integer|min:0', // Validasi untuk stok
        ]);

        // Jika SKU kosong atau tidak di-input, generate SKU otomatis
        if (!$request->has('sku') || empty($request->sku)) {
            do {
                // Generate SKU dengan format "SKU" diikuti dengan angka random unik
                $sku = 'SKU' . rand(1000, 9999);
            } while (Product::where('sku', $sku)->exists()); // Memastikan SKU unik

        } else {
            // Jika SKU di-input secara manual, gunakan SKU yang di-input user
            $sku = 'SKU' . $request->sku;
        }

        $productID = $request->id;

        if ($productID) {
            // Update item inventaris yang sudah ada
            $product = Product::updateOrCreate(
                ['id' => $productID],
                [
                    'name' => $request->name,
                    'sku' => $sku, // Menggunakan SKU yang sudah di-generate
                    'price' => $request->input('price'), // Tidak perlu number_format
                    'cost' => $request->input('cost'),   // Tidak perlu number_format
                    'stock' => $request->stock,
                ]
            );

            // Product item diperbarui
            return response()->json('Updated');
        } else {
            // Membuat item inventaris baru
            $product = Product::create([
                'name' => $request->name,
                'sku' => $sku, // Menggunakan SKU yang sudah di-generate
                'price' => $request->input('price'), // Tidak perlu number_format
                'cost' => $request->input('cost'),   // Tidak perlu number_format
                'stock' => $request->stock,
            ]);

            // Product item dibuat
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
        $product = Product::where('id', $id)->delete();
    }
}
