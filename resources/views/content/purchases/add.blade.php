@extends('layouts/layoutMaster')

@section('title', 'Insert - Purchase')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
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
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/purchase/form-management.js')}}"></script>
<script src="{{asset('assets/js/forms-editors.js')}}"></script>
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
      <div class="card-body">
        <form id="formValidationExamples" class="row g-3" method="POST" action="{{ route('purchases.store') }}">
          @csrf
          <!-- Supplier Section -->
          <div class="col-12">
            <h6 class="fw-semibold">1. Supplier</h6>
            <hr class="mt-0" />
          </div>

          <div class="col-md-6">
            <label class="form-label" for="formValidationSupplier">Supplier</label>
            <select id="formValidationSupplier" name="supplier_id" class="form-select select2" data-allow-clear="true" required>
              <option value="" disabled selected>Select Supplier</option>
              <!-- Populate dynamically -->
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierEmail">Supplier Email</label>
            <input type="text" id="supplierEmail" class="form-control" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierPhone">Supplier Phone</label>
            <input type="text" id="supplierPhone" class="form-control" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="supplierAddress">Supplier Address</label>
            <input type="text" id="supplierAddress" class="form-control" readonly>
          </div>

          <!-- Product Section -->
          <div class="col-12">
            <h6 class="fw-semibold">2. Product</h6>
            <hr class="mt-0" />
          </div>

          <div class="col-12">
            <div class="form-group position-relative">
              <input type="text" id="searchProduct" class="form-control" placeholder="Scan/Search Product by code and select" />
            </div>
          </div>

          <div class="col-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>QTY</th>
                  <th>Purchase Price (Rp)</th>
                  <!-- <th>Discount (Rp)</th>
                  <th>Tax %</th>
                  <th>Tax Amount (Rp)</th> -->
                  <th>Unit Cost (Rp)</th>
                  <th>Total Cost (Rp)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="productTableBody">
                <!-- Dynamically added rows will go here -->
              </tbody>
            </table>
          </div>

          <div class="row g-4">
            <div class="col-md-12 d-flex justify-content-end">
              <div class="card shadow-sm w-50">
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <td class="fw-medium text-secondary py-3">Order Tax</td>
                      <td class="text-end py-3 text-secondary">Rp <span id="displayOrderTax">0</span></td>
                    </tr>
                    <tr>
                      <td class="fw-medium text-secondary py-3">Discount</td>
                      <td class="text-end py-3 text-secondary">Rp <span id="displayDiscount">0</span></td>
                    </tr>
                    <tr>
                      <td class="fw-medium text-secondary py-3">Shipping</td>
                      <td class="text-end py-3 text-secondary">Rp <span id="displayShipping">0</span></td>
                    </tr>
                    <tr class="fw-bold text-dark">
                      <td class="py-3">Grand Total</td>
                      <td class="text-end text-primary py-3 fw-bold">Rp <span id="grandTotal">0</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>


          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label" for="reference">Reference</label>
              <input type="text" id="reference" name="reference" class="form-control" required>
            </div>

            <!-- Purchase Date -->
            <div class="col-md-6">
              <label class="form-label" for="purchaseDate">Purchase Date</label>
              <input type="text" id="purchaseDate" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-3">
              <label class="form-label" for="orderTax">Order Tax</label>
              <input type="number" class="form-control" id="orderTax" name="order_tax" value="0" min="0" placeholder="0">
            </div>
            <div class="col-md-3">
              <label class="form-label" for="discount">Discount</label>
              <input type="number" class="form-control" id="discount" name="discount" value="0" min="0" placeholder="0">
            </div>
            <div class="col-md-3">
              <label class="form-label" for="shipping">Shipping</label>
              <input type="number" class="form-control" id="shipping" name="shipping" value="0" min="0" placeholder="0">
            </div>
            <div class="col-md-3">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-select" required>
                <option value="" disabled selected>Select Status</option>
                <option value="Received">Received</option>
                <option value="Pending">Pending</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-3">

            <div class="">
              <label class="form-label" for="shipping">Description</label>
              <div id="snow-toolbar">
                <span class="ql-formats">
                  <select class="ql-font"></select>
                  <select class="ql-size"></select>
                </span>
                <span class="ql-formats">
                  <button class="ql-bold"></button>
                  <button class="ql-italic"></button>
                  <button class="ql-underline"></button>
                  <button class="ql-strike"></button>
                </span>
                <span class="ql-formats">
                  <select class="ql-color"></select>
                  <select class="ql-background"></select>
                </span>
                <span class="ql-formats">
                  <button class="ql-script" value="sub"></button>
                  <button class="ql-script" value="super"></button>
                </span>
                <span class="ql-formats">
                  <button class="ql-header" value="1"></button>
                  <button class="ql-header" value="2"></button>
                  <button class="ql-blockquote"></button>
                  <button class="ql-code-block"></button>
                </span>
              </div>
              <div id="snow-editor">

              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /FormValidation -->
</div>

@endsection