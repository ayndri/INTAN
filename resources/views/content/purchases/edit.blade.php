@extends('layouts/layoutMaster')

@section('title', 'Update')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('assets/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/purchase/edit-management.js')}}"></script>
<script src="{{asset('js/purchase/sweetalert-messages.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Purchase /</span> Insert
</h4>
<div class="row">
  <!-- FormValidation -->
  <div class="col-12">
    <div class="card">
      <!-- <h5 class="card-header">FormValidation</h5> -->
      <div class="card-body">

        <form id="formValidationExamples" class="row g-3" method="POST" action="{{ route('purchases.update', $purchase->id) }}">
          @csrf
          @method('PUT')

          <!-- Supplier Section -->
          <div class="col-12">
            <h6 class="fw-semibold">1. Supplier</h6>
            <hr class="mt-0" />
          </div>

          <div class="col-md-6">
            <label class="form-label" for="formValidationSupplier">Supplier</label>
            <select id="formValidationSupplier" name="supplier_id" class="form-select select2" required>
              <option value="" disabled>Select Supplier</option>
              @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}" {{ $supplier->id == $purchase->supplier_id ? 'selected' : '' }}>
                {{ $supplier->name }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierEmail">Supplier Email</label>
            <input type="text" id="supplierEmail" class="form-control" value="{{ optional($purchase->supplier)->email }}" readonly disabled>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierPhone">Supplier Phone</label>
            <input type="text" id="supplierPhone" class="form-control" value="{{ optional($purchase->supplier)->phone }}" readonly disabled>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierAddress">Supplier Address</label>
            <input type="text" id="supplierAddress" class="form-control" value="{{ optional($purchase->supplier)->address }}" readonly disabled>
          </div>

          <!-- Product Section -->
          <div class="col-12">
            <h6 class="fw-semibold">2. Product</h6>
            <hr class="mt-0" />
          </div>

          <div class="col-md-6">
            <label class="form-label" for="formValidationProduct">Product</label>
            <select id="formValidationProduct" name="product_id" class="form-select select2" required>
              <option value="" disabled>Select Product</option>
              @foreach($products as $product)
              <option value="{{ $product->id }}" {{ $product->id == $purchase->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="productQty">Product Quantity</label>
            <input type="text" id="productQty" class="form-control" value="{{ $purchase->product->stock }}" readonly disabled>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="productPrice">Selling Product Price</label>
            <input type="text" id="productPrice" class="form-control" value="{{ $purchase->product->price }}" readonly disabled>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="productCost">Cost Product Price</label>
            <input type="text" id="productCost" class="form-control" value="{{ $purchase->product->cost }}" readonly disabled>
          </div>

          <!-- Purchase Section -->
          <div class="col-12">
            <h6 class="fw-semibold">3. Purchase Product</h6>
            <hr class="mt-0" />
          </div>

          <div class="col-md-6">
            <label class="form-label" for="tanggalBeli">Tanggal Pembelian</label>
            <input type="text" class="form-control" name="tanggalBeli" id="tanggalBeli" placeholder="Tanggal Pembelian" value="{{ $purchase->purchase_date }}" />
          </div>

          <div class="col-md-6">
            <label class="form-label" for="qty">Quantity</label>
            <input type="number" min="1" id="qty" name="qty" class="form-control" value="{{ $purchase->quantity }}">
          </div>

          <div class="col-md-6">
            <label class="form-label" for="priceSt">Harga Satuan</label>
            <input type="text" id="priceSt" name="priceSt" class="form-control" value="{{ $purchase->product->price }}">
          </div>

          <div class="col-md-6">
            <label class="form-label" for="total">Cost Product Price</label>
            <input type="text" id="total" name="total" class="form-control" value="{{ $purchase->total }}" readonly disabled>
          </div>

          <div class="col-12">
            <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /FormValidation -->
</div>

<!-- Hidden elements for session messages -->
@if (session('success'))
<div id="success-message" style="display: none;">{{ session('success') }}</div>
@endif

@if (session('error'))
<div id="error-message" style="display: none;">{{ session('error') }}</div>
@endif
@endsection