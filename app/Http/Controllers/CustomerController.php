<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
  public function index()
  {
    return view('content.customers.index');
  }

  public function list(Request $request)
  {
    // Define the columns to map the request's order to the database fields
    $columns = [
      1 => 'id',
      2 => 'name',
      3 => 'code',        // Unique customer identifier
      4 => 'email',       // Customer email
      5 => 'phone',       // Customer phone
      6 => 'country_id',  // Country ID (we'll load the country name via relationship)
    ];

    $search = $request->input('search.value');

    // Get the total count of customers
    $totalData = Customer::count();
    $totalFiltered = $totalData;

    // Get pagination, order, and search parameters from the request
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    $customersQuery = Customer::with('country:id,name'); // Assuming there's a Country model with id and name

    // If search value is provided, filter the results
    if (!empty($search)) {
      $customersQuery->where(function ($query) use ($search) {
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
      $totalFiltered = $customersQuery->count();
    }

    // Apply pagination and ordering
    $customers = $customersQuery->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir)
      ->get();

    // Prepare data for response
    $data = [];
    if (!empty($customers)) {
      $ids = $start;
      foreach ($customers as $customer) {
        $nestedData['id'] = $customer->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $customer->name;
        $nestedData['code'] = $customer->code;
        $nestedData['email'] = $customer->email;
        $nestedData['phone'] = !empty($customer->phone_code) ? '+' . $customer->phone_code . ' ' . $customer->phone : $customer->phone;
        $nestedData['country'] = $customer->country ? $customer->country->name : 'N/A';

        // Determine if the avatar is a full URL or a relative path
        if (filter_var($customer->avatar, FILTER_VALIDATE_URL)) {
          $nestedData['avatar'] = $customer->avatar;
        } else {
          $nestedData['avatar'] = asset('storage/' . $customer->avatar);
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
      'customer_name' => 'required|string|max:255',
      'customer_email' => 'required|email|max:255|unique:customers,email,' . $request->id, // Unique email
      'phone_code' => 'nullable|string|max:10',
      'phone_number' => 'nullable|string|max:20',
      'country' => 'nullable|integer|exists:countries,id', // Assuming 'country' is the country_id
      'city' => 'nullable|integer|exists:cities,id', // Assuming 'city' is the city_id
      'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
      'description' => 'nullable|string|max:60',
    ]);

    // Get the customer ID (if updating an existing customer)
    $customerID = $request->id;
    $imagePath = null;

    if ($customerID) {
      // Find the existing customer
      $customer = Customer::findOrFail($customerID);

      if ($request->hasFile('customer_image')) {
        // Delete the old avatar if it exists
        if ($customer && $customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
          Storage::disk('public')->delete($customer->avatar);
        }

        // Store the new avatar
        $imagePath = $request->file('customer_image')->store('customer_avatars', 'public');
      } else {
        // Keep the old avatar path if a new image is not uploaded
        $imagePath = $customer->avatar;
      }

      // Update customer details
      $customer->update([
        'name' => $request->customer_name,
        'email' => $request->customer_email,
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
      if ($request->hasFile('customer_image')) {
        // Store the new avatar
        $imagePath = $request->file('customer_image')->store('customer_avatars', 'public');
      }

      // Generate unique code starting with "SUP"
      $uniqueCode = $this->generateUniqueCustomerCode();

      // Create a new customer
      $customer = Customer::create([
        'name' => $request->customer_name,
        'email' => $request->customer_email,
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
   * Generate a unique customer code with "SUP" prefix.
   */
  private function generateUniqueCustomerCode()
  {
    do {
      // Generate a random number with 3 or 4 digits
      $number = rand(100, 9999); // Random number between 100 and 9999
      $code = 'CUST' . $number;
    } while (Customer::where('code', $code)->exists());

    return $code;
  }

  public function destroy($id)
  {
    // Now delete the brand
    $customer = Customer::where('id', $id)->delete();

    return response()->json(['message' => 'Brand deleted and associated products updated successfully.']);
  }

  public function edit($id)
  {
    $where = ['id' => $id];

    // Retrieve the customer by id, including related city and country if necessary
    $customer = Customer::where($where)->first();

    if (!$customer) {
      return response()->json(['error' => 'Customer not found'], 404);
    }

    // Return the customer details, including city_id and country_id
    return response()->json([
      'id' => $customer->id,
      'name' => $customer->name,
      'email' => $customer->email,
      'phone_code' => $customer->phone_code,
      'phone' => $customer->phone,
      'avatar' => $customer->avatar,
      'description' => $customer->description,
      'country_id' => $customer->country_id,
      'city_id' => $customer->city_id,
    ]);
  }
}
