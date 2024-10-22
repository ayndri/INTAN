@extends('layouts/layoutMaster')

@section('title', 'Suupliers Management')

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
<script src="{{asset('js/supplier-management.js')}}"></script>
@endsection

@section('content')

<!-- Suupliers List Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4">
                <h5 class="card-title mb-0">Suupliers</h5>
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
                    <th>Name</th>
                    <th>Email</th> <!-- Mengganti Description menjadi SKU -->
                    <th>Phone Number</th>
                    <th>Transaction Count</th>
                    <th>Actions</th>

                </tr>
            </thead>
        </table>
    </div>
    <!-- Offcanvas to add new supplier -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCustomer" aria-labelledby="offcanvasAddCustomerLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddCustomerLabel" class="offcanvas-title">Add Suupliers</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form class="add-new-supplier pt-0" id="addNewCustomerForm">
                <input type="hidden" name="id" id="customer_id">

                <!-- Supplier Name Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-supplier-name">Supplier Name</label>
                    <input type="text" class="form-control" id="add-supplier-name" placeholder="Supplier Name" name="name" aria-label="Supplier Name" required />
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-supplier-email">Supplier Email</label>
                    <input type="email" class="form-control" id="add-supplier-email" placeholder="Supplier Email" name="email" aria-label="Supplier Email" required />
                </div>

                <!-- Phone Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-supplier-phone">Supplier Phone</label>
                    <input type="text" class="form-control" id="add-supplier-phone" placeholder="Supplier Phone" name="phone" aria-label="Supplier Phone" required />
                </div>

                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>


        </div>
    </div>
</div>
@endsection