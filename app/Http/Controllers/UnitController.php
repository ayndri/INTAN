<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
  public function index()
  {
    return view('content.units.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'units.id',
      2 => 'units.unit_name',
      3 => 'units.short_name',
      4 => 'product_count',
      5 => 'brands.created_at',
      6 => 'units.status',
    ];

    $search = $request->input('search.value'); // Get search value
    $statusFilter = $request->input('status_filter');
    $totalData = Unit::count(); // Total count of units
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    // Query with product count
    $unitsQuery = Unit::select('units.id', 'units.unit_name', 'units.status', 'units.short_name', 'units.created_at')
      ->withCount('products') // Count products per unit
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search and status filter functionality
    if (!empty($search) || !empty($statusFilter)) {
      $unitsQuery->where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'unit_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('units.id', 'LIKE', "%{$search}%")
              ->orWhere('units.unit_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('units.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('units.status', false); // Must match inactive
          }
        }
      });

      // Count filtered results
      $totalFiltered = Unit::where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'unit_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('units.id', 'LIKE', "%{$search}%")
              ->orWhere('units.unit_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('units.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('units.status', false); // Must match inactive
          }
        }
      })->count();
    }

    // Fetch the result
    $units = $unitsQuery->get();

    $data = [];
    if (!empty($units)) {
      $ids = $start;
      foreach ($units as $unit) {
        $nestedData['id'] = $unit->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['unit_name'] = $unit->unit_name;
        $nestedData['short_name'] = $unit->short_name;
        $nestedData['product_count'] = $unit->products_count;
        $nestedData['created_at'] = $unit->created_at->format('d M Y');
        $nestedData['status'] = $unit->status ? 'Active' : 'Inactive';
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
      'searchFilter' => $statusFilter
    ]);
  }

  public function store(Request $request)
  {
    // Validate the request
    $request->validate([
      'unit_name' => 'required|string|max:100',
      'short_name' => 'required|string',
      'status' => 'nullable|in:on,off',
    ]);

    // Get the unit ID (if updating an existing unit)
    $unitID = $request->id;

    if ($unitID) {
      // Find the existing unit
      $unit = Unit::findOrFail($unitID);

      // Check if the status is being set to false (inactive)
      if (!$request->status) {
        // Set unit_id to null for all products associated with this unit, if applicable
        // Update this based on your product relationship logic if necessary
        Product::where('unit_id', $unitID)->update(['unit_id' => null]);
      }

      // Update the existing unit
      $unit->update([
        'unit_name' => $request->unit_name,
        'short_name' => $request->short_name,
        'status' => $request->status ? true : false, // Use provided status (active/inactive)
      ]);

      // Return response
      return response()->json('Updated');
    } else {
      // Create a new unit
      $unit = Unit::create([
        'unit_name' => $request->unit_name,
        'short_name' => $request->short_name,
        'status' => $request->status ? true : false, // Use provided status
      ]);

      // Unit created
      return response()->json('Created');
    }
  }

  public function edit($id)
  {
    $where = ['id' => $id];

    // Retrieve the brand by id
    $unit = Unit::where($where)->first();

    return response()->json($unit);
  }

  public function destroy($id)
  {
    // Set all products' brand_id to null for the specific brand before deleting the brand
    Product::where('unit_id', $id)->update(['unit_id' => null]);

    // Now delete the brand
    $unit = Unit::where('id', $id)->delete();

    return response()->json(['message' => 'Unit deleted and associated products updated successfully.']);
  }
}
