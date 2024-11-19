<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index()
  {
    return view('content.categories.index');
  }

  public function store(Request $request)
  {
    // Validate the request
    $request->validate([
      'category_name' => 'required|string|max:100',
      'category_slug' => 'required|string',
      'status' => 'nullable|in:on,off',
    ]);

    // Get the category ID (if updating an existing category)
    $categoryID = $request->id;

    if ($categoryID) {
      // Find the existing category
      $category = Category::findOrFail($categoryID);

      // Check if the status is being set to false (inactive)
      if (!$request->status) {
        // Set unit_id to null for all products associated with this category, if applicable
        // Update this based on your product relationship logic if necessary
        Product::where('category_id', $categoryID)->update(['category_id' => null]);
      }

      // Update the existing category
      $category->update([
        'name' => $request->category_name,
        'slug' => $request->category_slug,
        'status' => $request->status ? true : false, // Use provided status (active/inactive)
      ]);

      // Return response
      return response()->json('Updated');
    } else {
      // Create a new category
      $category = Category::create([
        'name' => $request->category_name,
        'slug' => $request->category_slug,
        'status' => $request->status ? true : false, // Use provided status
      ]);

      // Category created
      return response()->json('Created');
    }
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'categories.id',
      2 => 'categories.name',
      3 => 'categories.slug',
      4 => 'categories.created_at',  // Added image column
      5 => 'categories.status', // Added created_at column
    ];

    $search = $request->input('search.value'); // Get search value
    $statusFilter = $request->input('status_filter');
    $totalData = Category::count(); // Total count of categories
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    // Query without product count
    $categoriesQuery = Category::select('categories.id', 'categories.name', 'categories.slug', 'categories.created_at', 'categories.status')
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir);

    // Handle search and status filter functionality
    if (!empty($search) || !empty($statusFilter)) {
      $categoriesQuery->where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'brand_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('categories.id', 'LIKE', "%{$search}%")
              ->orWhere('categories.brand_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('categories.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('categories.status', false); // Must match inactive
          }
        }
      });

      // Count filtered results
      $totalFiltered = Category::where(function ($query) use ($search, $statusFilter) {
        // Apply search filter for 'id' and 'brand_name'
        if (!empty($search)) {
          $query->where(function ($q) use ($search) {
            $q->where('categories.id', 'LIKE', "%{$search}%")
              ->orWhere('categories.brand_name', 'LIKE', "%{$search}%");
          });
        }

        // Apply status filter for 'active' or 'inactive'
        if (!empty($statusFilter)) {
          if ($statusFilter === 'Active') {
            $query->where('categories.status', true);  // Must match active
          } elseif ($statusFilter === 'Inactive') {
            $query->where('categories.status', false); // Must match inactive
          }
        }
      })->count();
    }

    // Fetch the result
    $categories = $categoriesQuery->get();

    $data = [];
    if (!empty($categories)) {
      $ids = $start;
      foreach ($categories as $category) {
        $nestedData['id'] = $category->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $category->name;
        $nestedData['slug'] = $category->slug;
        $nestedData['status'] = $category->status ? 'Active' : 'Inactive'; // Format status
        $nestedData['created_at'] = $category->created_at->format('d M Y'); // Format created_at as "25 May 2023"
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

  public function edit($id)
  {
    $where = ['id' => $id];

    // Retrieve the brand by id
    $category = Category::where($where)->first();

    return response()->json($category);
  }
}
