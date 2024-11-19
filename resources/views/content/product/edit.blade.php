@extends('layouts/layoutMaster')

@section('title', ' Create Product - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-layouts.js')}}"></script>
<script src="{{asset('js/dropzone-product.js')}}"></script>
<script src="{{asset('js/edit-product-management.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Product/</span> Create Product</h4>

<!-- Collapsible Section -->
<div class="row my-4">
  <div class="col">
    <div class="accordion" id="collapsibleSection">
      <div class="card accordion-item">
        <h2 class="accordion-header" id="headingDeliveryAddress">
          <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryAddress" aria-expanded="true" aria-controls="collapseDeliveryAddress"> Product Information </button>
        </h2>
        <div id="collapseDeliveryAddress" class="accordion-collapse collapse show" data-bs-parent="#collapsibleSection">
          <div class="accordion-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="add-product-name">Product Name</label>
                <input type="hidden" id="add-product-id" value="{{ $product->id }}">
                <input type="text" id="add-product-name" class="form-control" value="{{ $product-> name }}" placeholder="John Doe" />
              </div>
              <div class="col-md-6 align-items-end">
                <label class="form-label" for="add-product-sku">SKU</label>
                <div class="input-group">
                  <input type="text" id="add-product-sku" class="form-control" placeholder="Enter SKU" value="{{ $product->sku }}">
                  <button type="button" id="generate-sku-btn" class="btn btn-warning">Generate Code</button>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label mb-0">Category</label>
                  <a href="#" class="text-secondary text-sm text-decoration-none"><i class="ti ti-plus"></i> Add New</a>
                </div>
                <select name="category_id" id="add-product-category" class="form-control">
                  <option value="" disabled selected>Select Category</option>
                  @foreach ($categories as $category)
                  <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label mb-0">Brands</label>
                  <a href="#" class="text-secondary text-sm text-decoration-none"><i class="ti ti-plus"></i> Add New</a>
                </div>
                <select name="brand_id" id="add-product-brand" class="form-control">
                  <option value="" disabled selected>Select Brand</option>
                  @foreach ($brands as $brand)
                  <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                    {{ $brand->brand_name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label mb-0">Unit</label>
                  <a href="#" class="text-secondary text-sm text-decoration-none"><i class="ti ti-plus"></i> Add New</a>
                </div>
                <select name="unit_id" id="add-product-unit" class="form-control">
                  <option value="" disabled selected>Select Unit</option>
                  @foreach ($units as $unit)
                  <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                    {{ $unit->unit_name }} ({{ $unit->short_name }})
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 align-items-end">
                <label class="form-label" for="add-product-item-code">Item Code</label>
                <div class="input-group">
                  <input type="text" id="add-product-item-code" class="form-control" value="{{ $product->item_code }}" placeholder="Enter Item Code">
                  <button type="button" id="generate-item-code-btn" class="btn btn-warning">Generate Code</button>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label" for="add-product-description">Description</label>
                <textarea name="description-textarea" class="form-control" id="add-product-description" rows="2" placeholder="Enter description">{{ $product->description }}</textarea>
                <small class="text-muted">Maximum 60 Characters</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card accordion-item">
        <h2 class="accordion-header" id="headingPricingStocks">
          <button type="button" class="accordion-button collapsed show" data-bs-toggle="collapse" data-bs-target="#collapsePricingStocks" aria-expanded="false" aria-controls="collapsePricingStocks"> Pricing & Stocks </button>
        </h2>
        <div id="collapsePricingStocks" class="accordion-collapse collapse show" data-bs-parent="#collapsibleSection">
          <div class="accordion-body">
            <div class="row">
              <div class="col-md-12">
                <label class="form-label" for="add-product-type">Product Type</label>
                <div class="d-flex align-items-center gap-2">
                  <div class="flex-fill">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" for="add-product-single">
                        <input name="productType" class="form-check-input" type="radio" value="single" id="edit-product-single"
                          {{ $product->product_type == 'single' ? 'checked' : '' }} onclick="showSection('single')" />
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Single Product</span>
                        </span>
                      </label>
                    </div>
                  </div>
                  <div class="flex-fill">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" for="add-product-variable">
                        <input name="productType" class="form-check-input" type="radio" value="variable" id="edit-product-variable"
                          {{ $product->product_type == 'variable' ? 'checked' : '' }} onclick="showSection('variable')" />
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Variable Product</span>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Single Product Section -->
              <div id="singleProductSection" class="mt-3">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="add-product-quantity">Quantity</label>
                    <input type="number" id="add-product-quantity" value="{{ $product->quantity }}" class="form-control" placeholder="Enter quantity" min="0" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="add-product-price">Price</label>
                    <input type="number" id="add-product-price" class="form-control" value="{{ $product->sell_price }}" placeholder="Enter price" min="0" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="add-product-quantity-alert">Quantity Alert</label>
                    <input type="number" id="add-product-quantity-alert" value="{{ $product->quantity_alert }}" class="form-control" placeholder="Enter quantity alert" min="0" />
                  </div>
                </div>
              </div>

              <!-- Variable Product Section -->
              <div id="variableProductSection" class="mt-3" style="display: none;">
                <div class="mb-3">
                  <label for="add-product-variant-attribute" class="form-label">Variant Attribute</label>
                  <select class="form-control" id="add-product-variant-attribute" name="variant-attribute" required>
                    <option value="color">Color</option>
                    <option value="size">Size</option>
                    <option value="material">Material</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label" for="add-product-variant-options">Variant Options</label>
                  <div id="variant-options-container">
                    <div class="input-group mb-2 variant-option">
                      <input type="text" class="form-control" name="variant-options[]" placeholder="Enter value (e.g., blue)" required>
                      <button type="button" class="btn btn-danger remove-option-btn" onclick="removeOption(this)">Remove</button>
                    </div>
                  </div>
                  <button type="button" id="add-option-btn" class="btn btn-primary" onclick="addOption()">Add Option</button>
                </div>

                <h5 class="mt-4">Variant Details</h5>
                <table class="table">
                  <thead>
                    <tr>
                      <th>Variation</th>
                      <th>Variant Value</th>
                      <th>SKU</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="variant-details-container"></tbody>
                </table>
              </div>

              <div class="col-12">
                <div class="card">
                  <h5 class="card-header">Product Image</h5>
                  <div class="card-body">
                    <form action="/upload" class="dropzone needsclick" id="dropzone-multi" data-images='@json($product->images)'>
                      <div class="dz-message needsclick">
                        Drop files here or click to upload.
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="form-check">
            <!-- <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
            <label class="form-check-label" for="flexCheckDefault">
              Active
            </label> -->
          </div>
          <button type="submit" id="submitProductButton" class="btn btn-primary">Save Product</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function showSection(type) {
    if (type === 'single') {
      document.getElementById('singleProductSection').style.display = 'block';
      document.getElementById('variableProductSection').style.display = 'none';
    } else if (type === 'variable') {
      document.getElementById('singleProductSection').style.display = 'none';
      document.getElementById('variableProductSection').style.display = 'block';
    }
  }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let variantCount = 0;

  function addOption() {
    const variantOptionsContainer = document.getElementById('variant-options-container');
    const newOption = document.createElement('div');
    newOption.className = 'input-group mb-2 variant-option';
    newOption.innerHTML = `
      <input type="text" class="form-control" name="variant-options[]" placeholder="Enter value (e.g., blue)" required>
      <button type="button" class="btn btn-danger remove-option-btn" onclick="removeOption(this)">Remove</button>
    `;
    variantOptionsContainer.appendChild(newOption);
    updateVariantDetailsTable();
  }

  function removeOption(button) {
    const optionGroup = button.parentElement;
    optionGroup.remove();
    updateVariantDetailsTable();
  }

  function updateVariantDetailsTable() {
    const variantDetailsContainer = document.getElementById('variant-details-container');
    const variantOptions = document.querySelectorAll('#variant-options-container input[name="variant-options[]"]');
    const selectedAttribute = document.getElementById('variant-attribute').value;

    variantDetailsContainer.innerHTML = '';

    variantOptions.forEach(option => {
      const variantValue = option.value.trim();
      if (variantValue) {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${selectedAttribute}</td>
          <td>${variantValue}</td>
          <td><input type="text" class="form-control" placeholder="SKU"></td>
          <td>
            <button class="btn btn-outline-secondary" onclick="decrementQuantity(this)">-</button>
            <input type="number" class="form-control d-inline-block" value="1" min="1" style="width: 60px;">
            <button class="btn btn-outline-secondary" onclick="incrementQuantity(this)">+</button>
          </td>
          <td><input type="number" class="form-control" placeholder="Price"></td>
          <td>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#variantModal">+</button>
            <button class="btn btn-danger btn-sm" onclick="removeRow(this)">✗</button>
          </td>
        `;
        variantDetailsContainer.appendChild(row);
      }
    });
  }

  function incrementQuantity(button) {
    const quantityInput = button.previousElementSibling;
    quantityInput.value = parseInt(quantityInput.value) + 1;
  }

  function decrementQuantity(button) {
    const quantityInput = button.nextElementSibling;
    if (parseInt(quantityInput.value) > 1) {
      quantityInput.value = parseInt(quantityInput.value) - 1;
    }
  }

  function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
  }

  function saveVariantDetails() {
    // Save variant details from modal (you can add more specific logic here)
    $('#variantModal').modal('hide');
    alert('Variant details saved successfully!');
  }

  document.getElementById('variant-options-container').addEventListener('input', updateVariantDetailsTable);
</script>


@endsection