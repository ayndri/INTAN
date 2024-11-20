<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
  public function getCountryCity(Request $request, $country_id = null)
  {
    if (!$country_id) {
      // Fetch and return all countries without states or cities
      $countries = Country::all();
    } else {
      // Fetch cities only within the specified country
      $country = Country::with(['states.cities' => function ($query) {
        $query->select('id', 'name', 'state_id'); // Only retrieve necessary fields
      }])->find($country_id);

      // Flatten cities across all states in the specified country
      $cities = $country ? $country->states->flatMap->cities->unique('id')->values() : [];
      return response()->json($cities);
    }

    return response()->json($countries);
  }

  public function dashboard()
  {
    $customer = Customer::count();
    $sales = Sale::count();
    $products = Product::count();

    return view('content.home.home', [
      'customers' => $customer,
      'sales' => $sales,
      'products' => $products
    ]);
  }
}
