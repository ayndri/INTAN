<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
  public function index()
  {
    return view('content.supplier.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'code',        // Unique supplier identifier
      4 => 'email',       // Supplier email
      5 => 'phone',       // Supplier phone
      6 => 'country_id',  // Country ID (we'll load the country name via relationship)
    ];

    $search = $request->input('search.value');

    // Get the total count of suppliers
    $totalData = Supplier::count();
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters from the request
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    $suppliersQuery = Supplier::with('country:id,name'); // Assuming there's a Country model with id and name

    // If search value is provided, filter the results
    if (!empty($search)) {
      $suppliersQuery->where(function ($query) use ($search) {
        $query->where('id', 'LIKE', "%{$search}%")
          ->orWhere('name', 'LIKE', "%{$search}%")
          ->orWhere('code', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('phone', 'LIKE', "%{$search}%")
          // Concatenate phone_code and phone with a + for combined search
          ->orWhereRaw("CONCAT('+', phone_code, ' ', phone) LIKE ?", ["%{$search}%"])
          ->orWhereRaw("CONCAT(phone_code, phone) LIKE ?", ["%{$search}%"]);
      });

      // Count the filtered results
      $totalFiltered = $suppliersQuery->count();
    }

    // Apply pagination and ordering
    $suppliers = $suppliersQuery->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir)
      ->get();

    // Prepare data for response
    $data = [];
    if (!empty($suppliers)) {
      $ids = $start;
      foreach ($suppliers as $supplier) {
        $nestedData['id'] = $supplier->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $supplier->name;
        $nestedData['code'] = $supplier->code;
        $nestedData['email'] = $supplier->email;
        $nestedData['phone'] = !empty($supplier->phone_code) ? '+' . $supplier->phone_code . ' ' . $supplier->phone : $supplier->phone;
        $nestedData['country'] = $supplier->country ? $supplier->country->name : 'N/A';

        // Determine if the avatar is a full URL or a relative path
        if (filter_var($supplier->avatar, FILTER_VALIDATE_URL)) {
          $nestedData['avatar'] = $supplier->avatar;
        } else {
          $nestedData['avatar'] = asset('storage/' . $supplier->avatar);
        }

        $data[] = $nestedData;
      }
    }

    // Return JSON response
    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
  }

  public function store(Request $request)
  {
    // Validate the request
    $request->validate([
      'supplier_name' => 'required|string|max:255',
      'supplier_email' => 'required|email|max:255|unique:suppliers,email,' . $request->id, // Unique email
      'phone_code' => 'nullable|string|max:10',
      'phone_number' => 'nullable|string|max:20',
      'country' => 'nullable|integer|exists:countries,id', // Assuming 'country' is the country_id
      'city' => 'nullable|integer|exists:cities,id', // Assuming 'city' is the city_id
      'supplier_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
      'description' => 'nullable|string|max:60',
    ]);

    // Get the supplier ID (if updating an existing supplier)
    $supplierID = $request->id;
    $imagePath = null;

    if ($supplierID) {
      // Find the existing supplier
      $supplier = Supplier::findOrFail($supplierID);

      if ($request->hasFile('supplier_image')) {
        // Delete the old avatar if it exists
        if ($supplier && $supplier->avatar && Storage::disk('public')->exists($supplier->avatar)) {
          Storage::disk('public')->delete($supplier->avatar);
        }

        // Store the new avatar
        $imagePath = $request->file('supplier_image')->store('supplier_avatars', 'public');
      } else {
        // Keep the old avatar path if a new image is not uploaded
        $imagePath = $supplier->avatar;
      }

      // Update supplier details
      $supplier->update([
        'name' => $request->supplier_name,
        'email' => $request->supplier_email,
        'phone_code' => $request->phone_code,
        'phone' => $request->phone_number,
        'country_id' => $request->country,
        'city_id' => $request->city,
        'avatar' => $imagePath,
        'description' => $request->description,
      ]);

      // Return response for update
      return response()->json('Updated');
    } else {
      if ($request->hasFile('supplier_image')) {
        // Store the new avatar
        $imagePath = $request->file('supplier_image')->store('supplier_avatars', 'public');
      }

      // Generate unique code starting with "SUP"
      $uniqueCode = $this->generateUniqueSupplierCode();

      // Create a new supplier
      $supplier = Supplier::create([
        'name' => $request->supplier_name,
        'email' => $request->supplier_email,
        'phone_code' => $request->phone_code,
        'phone' => $request->phone_number,
        'country_id' => $request->country,
        'city_id' => $request->city,
        'avatar' => $imagePath,
        'description' => $request->description,
        'code' => $uniqueCode, // Assign the generated code
      ]);

      // Return response for creation
      return response()->json('Created');
    }
  }

  /**
   * Generate a unique supplier code with "SUP" prefix.
   */
  private function generateUniqueSupplierCode()
  {
    do {
      // Generate a random number with 3 or 4 digits
      $number = rand(100, 9999); // Random number between 100 and 9999
      $code = 'SUP' . $number;
    } while (Supplier::where('code', $code)->exists());

    return $code;
  }

  public function destroy($id)
  {
    // Now delete the brand
    $supplier = Supplier::where('id', $id)->delete();

    return response()->json(['message' => 'Brand deleted and associated products updated successfully.']);
  }

  public function edit($id)
  {
    $where = ['id' => $id];

    // Retrieve the supplier by id, including related city and country if necessary
    $supplier = Supplier::where($where)->first();

    if (!$supplier) {
      return response()->json(['error' => 'Supplier not found'], 404);
    }

    // Return the supplier details, including city_id and country_id
    return response()->json([
      'id' => $supplier->id,
      'name' => $supplier->name,
      'email' => $supplier->email,
      'phone_code' => $supplier->phone_code,
      'phone' => $supplier->phone,
      'avatar' => $supplier->avatar,
      'description' => $supplier->description,
      'country_id' => $supplier->country_id,
      'city_id' => $supplier->city_id,
    ]);
  }
}
