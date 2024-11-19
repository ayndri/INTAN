@extends('layouts/layoutMaster')

@section('title', 'Categories Management')

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
<script src="{{asset('js/categories-management.js')}}"></script>
@endsection

@section('content')

<!-- Categories List Table -->
<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4">
        <h5 class="card-title mb-0">Categories</h5>
      </div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-categories table">
      <thead class="border-top">
        <tr>
          <th></th>
          <th>Id</th>
          <th>Category</th>
          <th>Category Slug</th>
          <th>Created On</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new category -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCategory" aria-labelledby="offcanvasAddCategoryLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddCategoryLabel" class="offcanvas-title">Add Categories</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-category pt-0" id="addNewCategoryForm">
        <input type="hidden" name="id" id="category_id">

        <!-- Category Name Field -->
        <div class="mb-3">
          <label class="form-label" for="add-category-name">Category Name</label>
          <input type="text" class="form-control" id="add-category-name" placeholder="Category Name" name="category_name" aria-label="Category Name" />
        </div>

        <!-- Description Field (Optional) -->
        <div class="mb-3">
          <label class="form-label" for="add-category-slug">Category Slug</label>
          <input type="text" class="form-control" id="add-category-slug" placeholder="Category Slug" name="category_slug" aria-label="Category Slug" />
        </div>

        <!-- Status Field (Active/Inactive) -->
        <div class="mb-3">
          <label class="form-label mb-0" for="add-category-status">Status</label>
          <label class="switch">
            <input type="checkbox" name="status" id="add-category-status" class="switch-input" checked />
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
@endsection