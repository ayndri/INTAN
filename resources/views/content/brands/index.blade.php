@extends('layouts/layoutMaster')

@section('title', 'Brands Management')

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
<script src="{{asset('js/brands-management.js')}}"></script>
@endsection

@section('content')

<!-- Brands List Table -->
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4">
        <h5 class="card-title mb-0">Brands</h5>
      </div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-brands table">
      <thead class="border-top">
        <tr>
          <th></th>
          <th>Id</th>
          <th>Brand</th>
          <th>Logo</th> <!-- Mengganti Description menjadi SKU -->
          <th>Created On</th>
          <th>Status</th>
          <th>Actions</th>

        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new brand -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddBrand" aria-labelledby="offcanvasAddBrandLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddBrandLabel" class="offcanvas-title">Add Brands</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-brand pt-0" id="addNewBrandForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="brand_id">

        <!-- Brand Name Field -->
        <div class="mb-3">
          <label class="form-label" for="add-brand-name">Brand Name</label>
          <input type="text" class="form-control" id="add-brand-name" placeholder="Brand Name" name="brand_name" aria-label="Brand Name" />
        </div>

        <!-- Logo Field (Optional) -->
        <div class="mb-3">
          <label for="image-upload" style="font-size: 14px; margin-right: 12px;">Logo</label>
          <div style="display: flex; align-items: center;">

            <div style="position: relative; margin-right: 12px;">
              <input type="file" id="brand-image" name="brand_image" accept="image/*" hidden onchange="previewImage(event)">
              <div id="image-container" style="width: 120px; height: 120px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center;">
                <span id="add-image-text" style="font-size: 12px; color: #777;">Add Image</span>
              </div>
            </div>

            <button type="button" class="btn btn-success" style="padding: 8px 12px; border: none; color: white; border-radius: 4px; cursor: pointer;" onclick="document.getElementById('brand-image').click()">Change Image</button>
          </div>
        </div>

        <!-- Status Field (Active/Inactive) -->
        <div class="mb-3">
          <label class="form-label mb-0" for="add-brand-status">Status</label>
          <label class="switch">
            <input type="checkbox" class="switch-input" id="add-brand-status" name="brand_status" checked />
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
          </label>
        </div>

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