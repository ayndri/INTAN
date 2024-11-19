<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
  public function index()
  {
    return view('content.brands.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'brands.id',
      2 => 'brands.brand_name',
      3 => 'brands.status',
      4 => 'brands.image',  // Added image column
      5 => 'brands.created_at', // Added created_at column
    ];

    $search = $request->input('search.value'); // Get search value
    $statusFilter = $request->input('status_filter');
    $totalData = Brand::count(); // Total count of brands
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    // Query without product count
    $brandsQuery = Brand::select('brands.id', 'brands.brand_name', 'brands.status', 'brands.image', 'brands.created_at')
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search and status filter functionality
    if (!empty($search) || !empty($statusFilter)) {
      $brandsQuery->where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'brand_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('brands.id', 'LIKE', "%{$search}%")
              ->orWhere('brands.brand_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('brands.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('brands.status', false); // Must match inactive
          }
        }
      });

      // Count filtered results
      $totalFiltered = Brand::where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'brand_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('brands.id', 'LIKE', "%{$search}%")
              ->orWhere('brands.brand_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('brands.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('brands.status', false); // Must match inactive
          }
        }
      })->count();
    }

    // Fetch the result
    $brands = $brandsQuery->get();

    $data = [];
    if (!empty($brands)) {
      $ids = $start;
      foreach ($brands as $brand) {
        $nestedData['id'] = $brand->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['brand_name'] = $brand->brand_name;
        $nestedData['status'] = $brand->status ? 'Active' : 'Inactive'; // Format status
        $nestedData['image'] = asset('storage/' . $brand->image); // Include image column
        $nestedData['created_at'] = $brand->created_at->format('d M Y'); // Format created_at as "25 May 2023"
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
      'brand_name' => 'required|string|max:100',
      'brand_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
      'brand_status' => 'nullable|in:on,off',
    ]);

    // Get the brand ID (if updating an existing brand)
    $brandID = $request->id;
    $imagePath = null;

    if ($brandID) {
      // Find the existing brand
      $brand = Brand::findOrFail($brandID);

      if ($request->hasFile('brand_image')) {
        // Delete the old image if it exists
        if ($brand && $brand->brand_image && Storage::disk('public')->exists($brand->brand_image)) {
          Storage::disk('public')->delete($brand->brand_image);
        }

        // Store the new image
        $imagePath = $request->file('brand_image')->store('brand_images', 'public');
      } else {
        // Keep the old image path if a new image is not uploaded
        $imagePath = $brand->brand_image;
      }

      // Check if the status is being set to false (inactive)
      if (!$request->status) {
        // Set brand_id to null for all products associated with this brand
        Product::where('brand_id', $brandID)->update(['brand_id' => null]);
      }

      $brand->update([
        'brand_name' => $request->brand_name,
        'image' => $imagePath,
        'status' => $request->brand_status ? true : false,
      ]);

      // Return response
      return response()->json('Updated');
    } else {
      if ($request->hasFile('brand_image')) {
        $imagePath = $request->file('brand_image')->store('brand_images', 'public');
      }

      // Create a new brand
      $brand = Brand::create([
        'brand_name' => $request->brand_name,
        'image' => $imagePath,
        'status' => $request->brand_status ? true : false,
      ]);

      // Brand created
      return response()->json('Created');
    }
  }
  public function edit($id)
  {
    $where = ['id' => $id];

    // Retrieve the brand by id
    $brand = Brand::where($where)->first();

    return response()->json($brand);
  }

  public function destroy($id)
  {
    // Set all products' brand_id to null for the specific brand before deleting the brand
    Product::where('brand_id', $id)->update(['brand_id' => null]);

    // Now delete the brand
    $brand = Brand::where('id', $id)->delete();

    return response()->json(['message' => 'Brand deleted and associated products updated successfully.']);
  }
}
