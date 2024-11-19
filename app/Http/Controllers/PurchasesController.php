<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
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
      2 => 'suppliers.name', // Supplier Name
      3 => 'purchases.reference', // Reference
      4 => 'purchases.purchase_date', // Purchase Date
      5 => 'purchases.status', // Status
      6 => 'purchases.total', // Total
    ];

    $search = $request->input('search.value'); // Get search value
    $totalData = \App\Models\Purchase::count(); // Total count of purchases
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')] ?? 'purchases.id';
    $dir = $request->input('order.0.dir') ?? 'asc';

    // Query to join purchases with suppliers and get the purchase details
    $purchasesQuery = \App\Models\Purchase::select(
      'purchases.id',
      'suppliers.name as supplier_name',
      'purchases.reference',
      'purchases.purchase_date',
      'purchases.status',
      'purchases.total'
    )
      ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search functionality
    if (!empty($search)) {
      $purchasesQuery->where(function ($query) use ($search) {
        $query->where('purchases.id', 'LIKE', "%{$search}%")
          ->orWhere('suppliers.name', 'LIKE', "%{$search}%")
          ->orWhere('purchases.reference', 'LIKE', "%{$search}%")
          ->orWhere('purchases.purchase_date', 'LIKE', "%{$search}%")
          ->orWhere('purchases.status', 'LIKE', "%{$search}%")
          ->orWhere('purchases.total', 'LIKE', "%{$search}%");
      });

      // Count filtered results
      $totalFiltered = \App\Models\Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
        ->where(function ($query) use ($search) {
          $query->where('purchases.id', 'LIKE', "%{$search}%")
            ->orWhere('suppliers.name', 'LIKE', "%{$search}%")
            ->orWhere('purchases.reference', 'LIKE', "%{$search}%")
            ->orWhere('purchases.purchase_date', 'LIKE', "%{$search}%")
            ->orWhere('purchases.status', 'LIKE', "%{$search}%")
            ->orWhere('purchases.total', 'LIKE', "%{$search}%");
        })
        ->get()
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
        $nestedData['supplier_name'] = $purchase->supplier_name;
        $nestedData['reference'] = $purchase->reference;
        $nestedData['purchase_date'] = \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y, h:i A');
        $nestedData['status'] = $purchase->status;
        $nestedData['total'] = 'Rp ' . number_format($purchase->total, 0, ',', '.'); // Format as Rupiah
        $nestedData['actions'] = '<button class="btn btn-info">Edit</button> <button class="btn btn-danger">Delete</button>'; // Placeholder for actions
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
    // Validate input
    $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'reference' => 'required|unique:purchases,reference',
      'purchase_date' => 'required|date',
      'order_tax' => 'nullable|numeric|min:0',
      'discount' => 'nullable|numeric|min:0',
      'shipping' => 'nullable|numeric|min:0',
      'status' => 'required|in:Received,Pending',
      'products' => 'required|array|min:1', // Ensure at least one product is added
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.quantity' => 'required|integer|min:1',
      'products.*.unit_cost' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
      // Save purchase data
      $purchase = Purchase::create([
        'supplier_id' => $request->supplier_id,
        'reference' => $request->reference,
        'purchase_date' => $request->purchase_date,
        'tax' => $request->order_tax ?? 0,
        'discount' => $request->discount ?? 0,
        'shipping' => $request->shipping ?? 0,
        'total' => 0, // Will be calculated later
        'status' => $request->status,
      ]);

      $total = 0;

      // Save purchase details
      foreach ($request->products as $productData) {
        $product = Product::findOrFail($productData['product_id']);
        $subtotal = $productData['quantity'] * $productData['unit_cost'];

        PurchaseDetail::create([
          'purchase_id' => $purchase->id,
          'product_id' => $productData['product_id'],
          'quantity' => $productData['quantity'],
          'purchase_price' => $productData['unit_cost'],
          'subtotal' => $subtotal,
        ]);

        // Update total purchase value
        $total += $subtotal;

        // Update product stock
        $product->increment('quantity', $productData['quantity']);
      }

      // Calculate grand total with tax, shipping, and discount
      $grandTotal = $total + $purchase->tax + $purchase->shipping - $purchase->discount;
      $purchase->update(['total' => $grandTotal]);

      DB::commit();

      return response()->json(['message' => 'Success'], 200);
    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json([
        'message' => 'Failed to create purchase: ' . $e->getMessage(),
      ], 500);
    }
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
