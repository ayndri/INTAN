@extends('layouts/layoutMaster')

@section('title', 'Units Management')

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
<script src="{{asset('js/units-management.js')}}"></script>
@endsection

@section('content')

<!-- Units List Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4">
                <h5 class="card-title mb-0">Units</h5>
            </div>
            <div class="col-md-4 user_status"></div>
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-units table">
            <thead class="border-top">
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Status</th> <!-- Mengganti Description menjadi SKU -->
                    <th>Product</th>
                    <th>Actions</th>

                </tr>
            </thead>
        </table>
    </div>
    <!-- Offcanvas to add new unit -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUnit" aria-labelledby="offcanvasAddUnitLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUnitLabel" class="offcanvas-title">Add Units</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form class="add-new-unit pt-0" id="addNewUnitForm">
                <input type="hidden" name="id" id="unit_id">

                <!-- Unit Name Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-unit-name">Unit Name</label>
                    <input type="text" class="form-control" id="add-unit-name" placeholder="Unit Name" name="unit_name" aria-label="Unit Name" />
                </div>

                <!-- Description Field (Optional) -->
                <div class="mb-3">
                    <label class="form-label" for="add-unit-description">Description</label>
                    <textarea class="form-control" id="add-unit-description" placeholder="Description (optional)" name="description" aria-label="Description"></textarea>
                </div>

                <!-- Status Field (Active/Inactive) -->
                <div class="mb-3">
                    <label class="form-label" for="add-unit-status">Status</label>
                    <select class="form-control" id="add-unit-status" name="status" aria-label="Status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>

        </div>
    </div>
</div>
@endsection