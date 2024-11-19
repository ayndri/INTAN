@extends('layouts/layoutMaster')

@section('title', 'Suppliers Management')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/suppliers-management.js')}}"></script>
@endsection

@section('content')

<!-- Suppliers List Table -->
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4">
        <h5 class="card-title mb-0">Suppliers</h5>
      </div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-suppliers table">
      <thead class="border-top">
        <tr>
          <th></th>
          <th>Id</th>
          <th>Supplier Name</th>
          <th>Code</th> <!-- Mengganti Description menjadi SKU -->
          <th>Email</th>
          <th>Phone</th>
          <th>Country</th>
          <th>Actions</th>

        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new supplier -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddSupplier" aria-labelledby="offcanvasAddSupplierLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddSupplierLabel" class="offcanvas-title">Add Suppliers</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-supplier pt-0" id="addNewSupplierForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="supplier_id">

        <!-- Avatar Field -->
        <div class="mb-3">
          <label for="image-upload" style="font-size: 14px; margin-right: 12px;">Avatar</label>
          <div style="display: flex; align-items: center;">
            <div style="position: relative; margin-right: 12px;">
              <input type="file" id="supplier-image" name="supplier_image" accept="image/*" hidden onchange="previewImage(event)">
              <div id="image-container" style="width: 120px; height: 120px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center;">
                <span id="add-image-text" style="font-size: 12px; color: #777;">Profile photo</span>
              </div>
            </div>
            <button type="button" class="btn btn-success" style="padding: 8px 12px; border: none; color: white; border-radius: 4px; cursor: pointer;" onclick="document.getElementById('supplier-image').click()">Change Image</button>
          </div>
        </div>

        <!-- Supplier Name Field -->
        <div class="mb-3">
          <label class="form-label" for="add-supplier-name">Supplier Name</label>
          <input type="text" class="form-control" id="add-supplier-name" placeholder="Supplier Name" name="supplier_name" aria-label="Supplier Name" />
        </div>

        <!-- Supplier Email Field -->
        <div class="mb-3">
          <label class="form-label" for="add-supplier-email">Email</label>
          <input type="email" class="form-control" id="add-supplier-email" placeholder="Supplier Email" name="supplier_email" aria-label="Supplier Email" />
        </div>

        <!-- Phone Code and Phone Number Fields -->
        <div class="mb-3">
          <label class="form-label" for="phone_code">Phone</label>
          <div class="input-group">
            <select class="form-control" id="phone_code" name="phone_code" aria-label="Phone Code">
              <!-- Options will be populated by AJAX -->
            </select>
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter phone number">
          </div>
        </div>

        <!-- Country and City Fields -->
        <div class="row mb-3">
          <div class="col">
            <label for="country" class="form-label">Country</label>
            <select class="form-control" id="country" name="country" aria-label="Country">
              <option value="">Choose</option>
              <!-- Country options will be populated by AJAX -->
            </select>
          </div>
          <div class="col">
            <label for="city" class="form-label">City</label>
            <select class="form-control" id="city" name="city" aria-label="City" disabled>
              <option value="">Choose</option>
              <!-- City options will be populated based on the selected country -->
            </select>
          </div>
        </div>

        <!-- Description Field -->
        <div class="mb-3">
          <label for="description" class="form-label">Descriptions</label>
          <textarea class="form-control" id="description" name="description" rows="3" maxlength="60" placeholder="Enter description..."></textarea>
          <small id="descriptionHelp" class="form-text text-muted">Maximum 60 Characters</small>
        </div>

        <!-- Submit and Cancel Buttons -->
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>
</div>
<script>
  function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const imageContainer = document.getElementById('image-container');
        imageContainer.style.backgroundImage = `url(${e.target.result})`;
        document.getElementById('add-image-text').style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  }
</script>
@endsection