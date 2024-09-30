<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            3 => 'email',
            4 => 'phone',
            5 => 'transaction_count', // New column for the number of transactions
        ];

        $search = [];

        // Get the total count of customer items
        $totalData = Customer::count();
        $totalFiltered = $totalData;

        // Get the pagination, order, and search information from the request
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            // If no search value is provided, get all customers with transaction count, pagination, and ordering
            $customerItems = Customer::leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
                ->select('customers.*', DB::raw('COUNT(sales.id) as transaction_count')) // Count transactions per customer
                ->groupBy('customers.id') // Group by customer ID
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            // If search value is provided, filter the results
            $search = $request->input('search.value');

            $customerItems = Customer::leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
                ->select('customers.*', DB::raw('COUNT(sales.id) as transaction_count')) // Count transactions per customer
                ->where(function ($query) use ($search) {
                    $query->where('customers.id', 'LIKE', "%{$search}%")
                        ->orWhere('customers.name', 'LIKE', "%{$search}%")
                        ->orWhere('customers.email', 'LIKE', "%{$search}%")
                        ->orWhere('customers.phone', 'LIKE', "%{$search}%");
                })
                ->groupBy('customers.id') // Group by customer ID
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            // Count the filtered results
            $totalFiltered = Customer::leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
                ->where(function ($query) use ($search) {
                    $query->where('customers.id', 'LIKE', "%{$search}%")
                        ->orWhere('customers.name', 'LIKE', "%{$search}%")
                        ->orWhere('customers.email', 'LIKE', "%{$search}%")
                        ->orWhere('customers.phone', 'LIKE', "%{$search}%");
                })
                ->count();
        }

        $data = [];

        if (!empty($customerItems)) {
            // Provide a dummy ID for display purposes
            $ids = $start;

            foreach ($customerItems as $item) {
                $nestedData['id'] = $item->id;
                $nestedData['fake_id'] = ++$ids;
                $nestedData['name'] = $item->name;
                $nestedData['email'] = $item->email;
                $nestedData['phone'] = $item->phone;
                $nestedData['transaction_count'] = $item->transaction_count; // Display the number of transactions

                $data[] = $nestedData;
            }
        }

        if ($data) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'code' => 200,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'Internal Server Error',
                'code' => 500,
                'data' => [],
            ]);
        }
    }


    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255', // Validation for the customer name
            'email' => 'required|string|email|max:255|unique:customers,email,' . $request->id, // Email is required and must be unique (except for the current customer if updating)
            'phone' => 'required|string|max:20', // Validation for the phone number
        ]);

        // Get the customer ID (if updating an existing customer)
        $customerID = $request->id;

        if ($customerID) {
            // Find the existing customer
            $customer = Customer::findOrFail($customerID);

            // Update the existing customer
            $customer->update([
                'name' => $request->name,
                'email' => $request->email, // Use provided email
                'phone' => $request->phone, // Use provided phone number
            ]);

            // Return response for successful update
            return response()->json('Customer Updated');
        } else {
            // Create a new customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email, // Use provided email
                'phone' => $request->phone, // Use provided phone number
            ]);

            // Return response for successful creation
            return response()->json('Customer Created');
        }
    }

    public function edit($id)
    {
        $where = ['id' => $id];

        // Retrieve the customer by id
        $customer = Customer::where($where)->first();

        return response()->json($customer);
    }
}
