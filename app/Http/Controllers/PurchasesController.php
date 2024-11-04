<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
  public function index()
  {
    return view('content.purchases.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'purchases.id',
      2 => 'products.name', // Correct the field name to 'name' from 'products' table
      3 => 'suppliers.name', // Update supplier_name to name from 'suppliers' table
      4 => 'purchases.purchase_date',
      5 => 'purchases.cost_price',
      6 => 'purchases.total',
    ];

    $search = $request->input('search.value'); // Get search value
    $totalData = \App\Models\Purchase::count(); // Total count of purchases
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    // Query to join purchases with products and suppliers
    $purchasesQuery = \App\Models\Purchase::select('purchases.id', 'products.name as product_name', 'suppliers.name as supplier_name', 'purchases.purchase_date', 'purchases.cost_price', 'purchases.total')
      ->join('products', 'purchases.product_id', '=', 'products.id')
      ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search functionality
    if (!empty($search)) {
      $purchasesQuery->where(function ($query) use ($search) {
        $query->where('purchases.id', 'LIKE', "%{$search}%")
          ->orWhere('products.name', 'LIKE', "%{$search}%") // Corrected field name for product search
          ->orWhere('suppliers.name', 'LIKE', "%{$search}%"); // Use 'name' for supplier search
      });

      // Count filtered results
      $totalFiltered = \App\Models\Purchase::join('products', 'purchases.product_id', '=', 'products.id')
        ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
        ->where(function ($query) use ($search) {
          $query->where('purchases.id', 'LIKE', "%{$search}%")
            ->orWhere('products.name', 'LIKE', "%{$search}%") // Corrected field name for product search
            ->orWhere('suppliers.name', 'LIKE', "%{$search}%"); // Use 'name' for supplier search
        })
        ->count();
    }

    // Fetch the result
    $purchases = $purchasesQuery->get();

    $data = [];
    if (!empty($purchases)) {
      $ids = $start;
      foreach ($purchases as $purchase) {
        $nestedData['id'] = $purchase->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['product_name'] = $purchase->product_name; // 'product_name' will now reference the correct column alias
        $nestedData['supplier_name'] = $purchase->supplier_name; // 'supplier_name' references the 'name' field from 'suppliers' table
        $nestedData['purchase_date'] = Carbon::parse($purchase->purchase_date)->format('d M Y, h:i A'); // Format the purchase_date
        $nestedData['cost_price'] = 'Rp ' . number_format($purchase->cost_price, 0, ',', '.'); // Format as Rupiah
        $nestedData['total_price'] = 'Rp ' . number_format($purchase->total, 0, ',', '.'); // Format as Rupiah
        $data[] = $nestedData;
      }
    }

    // Return JSON response
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'data' => $data,
      'search' => $search
    ]);
  }

  public function insert(Request $request)
  {
    return view('content.purchases.add');
  }

  public function listSupplier(Request $request, $id_supplier = null)
  {
    if ($id_supplier) {
      $supplier = Supplier::find($id_supplier);
      return response()->json($supplier);
    } else {
      $suppliers = Supplier::all();
      return response()->json($suppliers);
    }
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

  public function store(Request $request)
  {
    DB::beginTransaction();
    // Validate the input
    $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'product_id' => 'required|exists:products,id',
      'qty' => 'required|integer|min:1',
      'tanggalBeli' => 'required|date',
    ]);

    // Calculate total price (assuming price is being fetched and passed)
    $product = Product::find($request->product_id);
    $total = $product->cost * $request->qty; // Assuming 'cost' is the cost price

    // Create a purchase record
    $purchase = Purchase::create([
      'product_id' => $request->product_id,
      'supplier_id' => $request->supplier_id,
      'quantity' => $request->qty,
      'cost_price' => $product->cost,
      'total' => $total,
      'purchase_date' => $request->tanggalBeli,
    ]);

    // Create inventory movement
    InventoryMovement::create([
      'product_id' => $request->product_id,
      'purchase_id' => $purchase->id,
      'type' => 'in', // For incoming stock
      'quantity' => $request->qty,
      'transaction_date' => now(),
      'description' => 'Stock received from purchase',
    ]);

    // Create accounting entry for the expense
    AccountingEntry::create([
      'description' => 'Purchase of ' . $product->name,
      'amount' => $total,
      'type' => 'expense',
      'entry_date' => now(),
      'purchase_id' => $purchase->id,
    ]);

    $product->stock += $request->qty;
    $product->save();

    DB::commit();

    // Redirect or return response
    return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully!');
  }

  public function edit($id)
  {
    $purchase = Purchase::with(['supplier', 'product'])->find($id); // Fetch the purchase along with supplier and product
    $suppliers = Supplier::all(); // Fetch all suppliers
    $products = Product::all(); // Fetch all products

    return view('content.purchases.edit', compact('purchase', 'suppliers', 'products'));
  }

  public function update(Request $request, $id)
  {
    DB::beginTransaction();
    try {
      // Validate the input
      $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'product_id' => 'required|exists:products,id',
        'qty' => 'required|integer|min:1',
        'tanggalBeli' => 'required|date',
      ]);

      // Find the existing purchase record
      $purchase = Purchase::findOrFail($id);
      // Get the current product to calculate stock changes
      $product = Product::findOrFail($request->product_id);

      // Calculate total price
      $total = $product->cost * $request->qty; // Assuming 'cost' is the cost price

      // Adjust the stock based on the old quantity
      $product->stock += ($request->qty - $purchase->quantity); // Adjust stock based on difference
      $product->save();

      // Update the purchase record
      $purchase->update([
        'product_id' => $request->product_id,
        'supplier_id' => $request->supplier_id,
        'quantity' => $request->qty,
        'cost_price' => $product->cost,
        'total' => $total,
        'purchase_date' => $request->tanggalBeli,
      ]);



      // Optionally, update inventory movement if needed
      InventoryMovement::create([
        'product_id' => $request->product_id,
        'purchase_id' => $purchase->id,
        'type' => 'in', // For incoming stock
        'quantity' => $request->qty - $purchase->quantity, // Adjust based on the change in quantity
        'transaction_date' => now(),
        'description' => 'Stock adjusted from purchase edit',
      ]);

      // Create accounting entry for the expense
      AccountingEntry::create([
        'description' => 'Update purchase of ' . $product->name,
        'amount' => $total,
        'type' => 'expense',
        'entry_date' => now(),
        'purchase_id' => $purchase->id,
      ]);

      DB::commit();

      // Redirect or return response
      return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully!');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back()->with('error', 'Failed to update purchase: ' . $e->getMessage());
    }
  }
}
