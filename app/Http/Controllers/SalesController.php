<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
  public function index()
  {
    return view('content.sales.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'sales.id',
      2 => 'customers.name',
      3 => 'sales.sale_date',
      4 => 'sales.status',
      5 => 'sales.total',
    ];

    $search = $request->input('search.value'); // Get search value
    $totalData = \App\Models\Sale::count(); // Total count of sales
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')] ?? 'sales.id';
    $dir = $request->input('order.0.dir') ?? 'asc';

    // Query to join sales with customers and suppliers
    $salesQuery = \App\Models\Sale::select(
      'sales.id',
      'sales.sale_date',
      'sales.total',
      'sales.status',
      'customers.name as customer_name',
    )
      ->join('customers', 'sales.customer_id', '=', 'customers.id')
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search functionality
    if (!empty($search)) {
      $salesQuery->where(function ($query) use ($search) {
        $query->where('sales.id', 'LIKE', "%{$search}%")
          ->orWhere('customers.name', 'LIKE', "%{$search}%")
          ->orWhere('sales.status', 'LIKE', "%{$search}%");
      });

      // Count filtered results
      $totalFiltered = \App\Models\Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
        ->where(function ($query) use ($search) {
          $query->where('sales.id', 'LIKE', "%{$search}%")
            ->orWhere('customers.name', 'LIKE', "%{$search}%")
            ->orWhere('sales.status', 'LIKE', "%{$search}%");
        })
        ->count();
    }

    // Fetch the result
    $sales = $salesQuery->get();

    $data = [];
    if (!empty($sales)) {
      $ids = $start;
      foreach ($sales as $sale) {
        $nestedData['id'] = $sale->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['customer_name'] = $sale->customer_name;
        $nestedData['sale_date'] = \Carbon\Carbon::parse($sale->sale_date)->format('d M Y, h:i A');
        $nestedData['status'] = ucfirst($sale->status);
        $nestedData['total_price'] = 'Rp ' . number_format($sale->total, 0, ',', '.'); // Format as Rupiah
        $data[] = $nestedData;
      }
    }

    // Return JSON response
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'data' => $data,
      'search' => $search,
    ]);
  }


  public function listProduct(Request $request, $id_product = null)
  {
    if ($id_product) {
      // Retrieve the product using find
      $product = Product::find($id_product);

      // Check if the product exists
      if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
      }

      return response()->json($product);
    } else {
      // If no product ID is provided, retrieve all products
      $products = Product::all();

      return response()->json($products);
    }
  }

  public function listCustomer(Request $request, $id_supplier = null)
  {
    if ($id_supplier) {
      $supplier = Customer::find($id_supplier);
      return response()->json($supplier);
    } else {
      $suppliers = Customer::all();
      return response()->json($suppliers);
    }
  }

  public function insert(Request $request)
  {
    return view('content.sales.add');
  }

  public function store(Request $request)
  {
    // Validasi input dari form
    $request->validate([
      'customer_id' => 'required|exists:customers,id',
      'sale_date' => 'required|date',
      'order_type' => 'required|in:online,offline',
      'order_tax' => 'nullable|numeric|min:0',
      'discount' => 'nullable|numeric|min:0',
      'shipping' => 'nullable|numeric|min:0',
      'status' => 'required|in:pending,in-progress,completed,cancelled',
      'products' => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.quantity' => 'required|integer|min:1',
      'products.*.unit_cost' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
      // Hitung subtotal berdasarkan produk
      $subtotal = 0;

      // Buat catatan sale
      $sale = Sale::create([
        'customer_id' => $request->customer_id,
        'sale_date' => $request->sale_date,
        'order_type' => $request->order_type,
        'status' => $request->status,
        'tax' => $request->order_tax ?? 0,
        'discount' => $request->discount ?? 0,
        'shipping' => $request->shipping ?? 0,
        'total' => 0, // Akan dihitung nanti
      ]);

      // Simpan detail produk yang terjual
      foreach ($request->products as $productData) {
        $product = Product::findOrFail($productData['product_id']);
        $totalCost = $productData['quantity'] * $productData['unit_cost'];

        SaleProduct::create([
          'sale_id' => $sale->id,
          'product_id' => $productData['product_id'],
          'quantity' => $productData['quantity'],
          'selling_price' => $productData['unit_cost'],
          'total' => $totalCost,
        ]);

        // Update subtotal dan stok produk
        $subtotal += $totalCost;
        $product->decrement('quantity', $productData['quantity']);
      }

      // Hitung total akhir dengan pajak, diskon, dan pengiriman
      $grandTotal = $subtotal + $sale->tax + $sale->shipping - $sale->discount;
      $sale->update(['total' => $grandTotal]);

      DB::commit();

      return response()->json(['message' => 'Sale successfully created.'], 201);
    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json(['message' => 'Error creating sale: ' . $e->getMessage()], 500);
    }
  }
}
