<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

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
            4 => 'product_count' // Custom column for the product count
        ];

        $search = $request->input('search.value'); // Get search value
        $totalData = Brand::count(); // Total count of brands
        $totalFiltered = $totalData;

        // Get pagination, order, and search parameters
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        // Query with product count
        $brandsQuery = Brand::select('brands.id', 'brands.brand_name', 'brands.status')
            ->withCount('products') // Count products per brand
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir);

        // Handle search functionality
        if (!empty($search)) {
            $brandsQuery->where(function ($query) use ($search) {
                $query->where('brands.id', 'LIKE', "%{$search}%")
                    ->orWhere('brands.brand_name', 'LIKE', "%{$search}%");

                // If search matches 'active' or 'inactive', apply status filter
                if (strtolower(trim($search)) === 'active') {
                    $query->orWhere('brands.status', true); // Active status
                } elseif (strtolower(trim($search)) === 'inactive') {
                    $query->orWhere('brands.status', false); // Inactive status
                }
            });

            // Count filtered results
            $totalFiltered = Brand::where(function ($query) use ($search) {
                $query->where('brands.id', 'LIKE', "%{$search}%")
                    ->orWhere('brands.brand_name', 'LIKE', "%{$search}%");

                if (strtolower(trim($search)) === 'active') {
                    $query->orWhere('brands.status', true); // Active status
                } elseif (strtolower(trim($search)) === 'inactive') {
                    $query->orWhere('brands.status', false); // Inactive status
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
                $nestedData['product_count'] = $brand->products_count; // Count products
                $data[] = $nestedData;
            }
        }

        // Return JSON response
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }



    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'brand_name' => 'required|string|max:100', // Validation for the brand name
            'description' => 'nullable|string', // Description is optional
            'status' => 'required|boolean', // Validation for status (true/false)
        ]);

        // Get the brand ID (if updating an existing brand)
        $brandID = $request->id;

        if ($brandID) {
            // Find the existing brand
            $brand = Brand::findOrFail($brandID);

            // Check if the status is being set to false (inactive)
            if ($request->status == false) {
                // Set brand_id to null for all products associated with this brand
                Product::where('brand_id', $brandID)->update(['brand_id' => null]);
            }

            // Update the existing brand
            $brand->update([
                'brand_name' => $request->brand_name,
                'description' => $request->description, // Use provided description
                'status' => $request->status, // Use provided status (active/inactive)
            ]);

            // Return response
            return response()->json('Updated');
        } else {
            // Create a new brand
            $brand = Brand::create([
                'brand_name' => $request->brand_name,
                'description' => $request->description, // Use provided description
                'status' => $request->status, // Use provided status
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
