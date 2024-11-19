<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
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
      2 => 'products.name',
      3 => 'customers.name',
      4 => 'sales.sale_date',
      5 => 'sale_product.selling_price',
      6 => 'sales.total',
    ];

    $search = $request->input('search.value'); // Get search value
    $totalData = \App\Models\Sale::count(); // Total count of sales
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')] ?? 'sales.id';
    $dir = $request->input('order.0.dir') ?? 'asc';

    // Query to join sales with customers and aggregate products
    $salesQuery = \App\Models\Sale::select(
      'sales.id',
      'sales.sale_date',
      'sales.total',
      'customers.name as customer_name',
      DB::raw('GROUP_CONCAT(products.name SEPARATOR ", ") as product_names') // Aggregate product names
    )
      ->join('customers', 'sales.customer_id', '=', 'customers.id')
      ->join('sale_product', 'sales.id', '=', 'sale_product.sale_id')
      ->join('products', 'sale_product.product_id', '=', 'products.id')
      ->groupBy('sales.id', 'sales.sale_date', 'sales.total', 'customers.name')
      ->offset($start)
      ->limit($limit)
      ->orderByRaw("GROUP_CONCAT(products.name ORDER BY products.name SEPARATOR ', ') {$dir}"); // Order by aggregated product names

    // Handle search functionality
    if (!empty($search)) {
      $salesQuery->where(function ($query) use ($search) {
        $query->where('sales.id', 'LIKE', "%{$search}%")
          ->orWhere('customers.name', 'LIKE', "%{$search}%")
          ->orWhere('products.name', 'LIKE', "%{$search}%");
      });

      // Count filtered results
      $totalFiltered = \App\Models\Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
        ->join('sale_product', 'sales.id', '=', 'sale_product.sale_id')
        ->join('products', 'sale_product.product_id', '=', 'products.id')
        ->where(function ($query) use ($search) {
          $query->where('sales.id', 'LIKE', "%{$search}%")
            ->orWhere('customers.name', 'LIKE', "%{$search}%")
            ->orWhere('products.name', 'LIKE', "%{$search}%");
        })
        ->groupBy('sales.id')
        ->get()
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
        $nestedData['product_names'] = $sale->product_names; // Aggregated product names
        $nestedData['customer_name'] = $sale->customer_name;
        $nestedData['sale_date'] = \Carbon\Carbon::parse($sale->sale_date)->format('d M Y, h:i A');
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
}
