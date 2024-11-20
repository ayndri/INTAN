<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function App\Helpers\formatRupiah;

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

    // Revenue
    $revenue = DB::table('sales')->sum('total'); // Total sales jika tersedia

    // Expenses
    $expenses = DB::table('purchases')
      ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
      ->select(DB::raw('
        SUM(purchase_details.subtotal) + SUM(purchases.tax) + SUM(purchases.shipping) - SUM(purchases.discount) AS total_expenses
    '))
      ->value('total_expenses');

    // Profit
    $profit = $revenue - $expenses;

    // Total Expenses bulan ini
    $currentMonthExpenses = DB::table('purchases')
      ->whereMonth('purchase_date', now()->month)
      ->whereYear('purchase_date', now()->year)
      ->sum('total');

    // Total Expenses bulan lalu
    $lastMonthExpenses = DB::table('purchases')
      ->whereMonth('purchase_date', now()->subMonth()->month)
      ->whereYear('purchase_date', now()->subMonth()->year)
      ->sum('total');

    // Selisih pengeluaran
    $increaseInExpenses = $currentMonthExpenses - $lastMonthExpenses;

    // Menghitung persentase perubahan
    if ($lastMonthExpenses > 0) {
      $percentageChange = ($increaseInExpenses / $lastMonthExpenses) * 100;
    } else {
      $percentageChange = 100; // Jika bulan lalu nol, set default 100%
    }

    // Format persentase dan perubahan menjadi angka untuk tampilan
    $formattedIncrease = number_format($increaseInExpenses, 0, ',', '.');
    $formattedPercentage = number_format($percentageChange, 0);

    $popularProducts = Product::with(['images' => function ($query) {
      $query->limit(1); // Ambil hanya gambar pertama untuk setiap produk
    }])
      ->orderBy('quantity', 'desc') // Atur berdasarkan popularitas atau jumlah
      ->limit(6) // Batasi ke 6 produk populer
      ->get();

    foreach ($popularProducts as $p) {
      $p->sell_price = formatRupiah($p->sell_price);
    }

    return view('content.home.home', [
      'customers' => $customer,
      'sales' => $sales,
      'products' => $products,
      'expenses' => formatRupiah($expenses),
      'profit' => formatRupiah($profit),
      'revenue' => formatRupiah($revenue),
      'increaseInExpenses' => formatRupiah($increaseInExpenses),
      'formattedPercentage' => $formattedPercentage,
      'popularProducts' => $popularProducts
    ]);
  }
}
