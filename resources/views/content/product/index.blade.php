@extends('layouts/layoutMaster')

@section('title', 'Product Management')

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
<script src="{{asset('js/product-management.js')}}"></script>
@endsection

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-8 col-md-4 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Product</span>
                        <div class="d-flex align-items-end mt-2">
                            <h3 class="mb-0 me-2">{{$totalProduct}}</h3>
                            <!-- <small class="text-success">(100%)</small> -->
                        </div>
                        <small>Total Product</small>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="ti ti-package ti-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-4 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Stok terbanyak</span>
                        <div class="d-flex align-items-end mt-2">
                            <h3 class="mb-0 me-2">{{$mostQuantity}}</h3>
                            <!-- <small class="text-success">(+95%)</small> -->
                        </div>
                        <small>{{ $mostProduct }}</small>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="ti ti-user-check ti-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-4 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Stok tersedikit</span>
                        <div class="d-flex align-items-end mt-2">
                            <h3 class="mb-0 me-2">{{$lessQuantity}}</h3>
                            <!-- <small class="text-success">(0%)</small> -->
                        </div>
                        <small>{{$lessProduct}}</small>
                    </div>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="ti ti-users ti-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inventories List Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Search Filter</h5>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-products table">
            <thead class="border-top">
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>SKU</th> <!-- Mengganti Description menjadi SKU -->
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Cost</th> <!-- Menambahkan kolom Cost -->
                    <th>Actions</th>

                </tr>
            </thead>
        </table>
    </div>
    <!-- Offcanvas to add new product -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddProduct" aria-labelledby="offcanvasAddProductLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddProductLabel" class="offcanvas-title">Add Product</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form class="add-new-product pt-0" id="addNewProductForm">
                <input type="hidden" name="id" id="product_id">
                <!-- Name Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-product-name">Name</label>
                    <input type="text" class="form-control" id="add-product-name" placeholder="Nama Barang" name="name" aria-label="Nama" />
                </div>

                <!-- Price Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-product-price">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="add-product-price" placeholder="100.000" name="price" aria-label="Price" />
                    </div>
                </div>

                <!-- Cost Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-product-cost">Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="add-product-cost" placeholder="50.000" name="cost" aria-label="Cost" />
                    </div>
                </div>

                <!-- Stock Field -->
                <div class="mb-3">
                    <label class="form-label" for="add-product-stock">Stock</label>
                    <input type="number" id="add-product-stock" class="form-control" placeholder="0" aria-label="Stock" name="stock" />
                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
</div>
@endsection