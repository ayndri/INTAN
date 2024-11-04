$(document).ready(function () {
  // Initialize Select2 for dropdowns
  $('.select2').select2({
    placeholder: 'Select an option',
    allowClear: true
  });

  // Initialize Flatpickr for the purchase date input
  $('#tanggalBeli').flatpickr({
    enableTime: true,
    altFormat: 'Y-m-dTH:i:S',
    onReady: function (selectedDates, dateStr, instance) {
      if (instance.isMobile) {
        instance.mobileInput.setAttribute('step', null);
      }
    }
  });

  // Load supplier options via AJAX
  loadOptions('/purchases/suppliers', '#formValidationSupplier');

  // Load product options via AJAX
  loadOptions('/purchases/products', '#formValidationProduct');

  $('#tanggalBeli').prop('disabled', true);
  // Handle supplier selection
  $('#formValidationSupplier').on('change', function () {
    const supplierId = $(this).val();
    if (supplierId) {
      fetchSupplierDetails(supplierId);
      // Enable product selection
      $('#formValidationProduct').prop('disabled', false);
    } else {
      clearSupplierDetails();
    }
    validateSubmitButton();
  });

  // Handle product selection
  $('#formValidationProduct').on('change', function () {
    const productId = $(this).val();
    if (productId) {
      fetchProductDetails(productId);
      // Enable quantity and date input
      $('#qty, #tanggalBeli').prop('readonly', false).prop('disabled', false);
    } else {
      clearProductDetails();
    }
    validateSubmitButton();
  });

  // Handle quantity change
  $('#qty').on('input', validateSubmitButton);
  $('#tanggalBeli').on('change', validateSubmitButton);

  // Function to load options via AJAX
  function loadOptions(url, selectId) {
    $.ajax({
      url: url,
      method: 'GET',
      success: function (data) {
        const select = $(selectId);
        select.empty().append('<option value="" disabled selected>Select an option</option>');
        data.forEach(item => {
          select.append(`<option value="${item.id}">${item.name}</option>`);
        });
      },
      error: function () {
        alert('Failed to load options');
      }
    });
  }

  // Function to fetch supplier details
  function fetchSupplierDetails(supplierId) {
    $.ajax({
      url: `/purchases/suppliers/${supplierId}`,
      method: 'GET',
      success: function (supplier) {
        $('#supplierEmail').val(supplier.email);
        $('#supplierPhone').val(supplier.phone);
        $('#supplierAddress').val(supplier.address);
      },
      error: function () {
        alert('Failed to fetch supplier details');
      }
    });
  }

  // Function to clear supplier details
  function clearSupplierDetails() {
    $('#supplierEmail, #supplierPhone, #supplierAddress').val('');
  }

  // Function to fetch product details
  function fetchProductDetails(productId) {
    $.ajax({
      url: `/purchases/products/${productId}`,
      method: 'GET',
      success: function (product) {
        $('#productQty').val(product.stock);
        $('#productPrice').val(formatRupiah(product.price));
        $('#productCost').val(formatRupiah(product.cost));
        $('#priceSt').val(formatRupiah(product.cost));
        calculateTotal();
      },
      error: function () {
        alert('Failed to fetch product details');
      }
    });
  }

  // Function to clear product details
  function clearProductDetails() {
    $('#productPrice, #productCost, #productQty').val('');
  }

  // Function to format number as Rupiah
  function formatRupiah(amount) {
    return (
      'Rp ' +
      Number(amount).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      })
    );
  }

  $('#qty').on('input', calculateTotal);

  // Function to calculate total cost
  function calculateTotal() {
    const quantity = $('#qty').val();
    const price = parseFloat(
      $('#priceSt')
        .val()
        .replace(/[Rp .]/g, '')
        .replace(',', '.')
    );

    if (!isNaN(quantity) && !isNaN(price)) {
      const total = quantity * price;
      $('#total').val(formatRupiah(total)); // Set total formatted to Rupiah
    } else {
      $('#total').val(''); // Clear total if input is invalid
    }
  }

  // Validate whether to enable the submit button
  function validateSubmitButton() {
    const isSupplierSelected = $('#formValidationSupplier').val() !== null;
    const isProductSelected = $('#formValidationProduct').val() !== null;
    const isQtyFilled = $('#qty').val() > 0;
    const isDateFilled = $('#tanggalBeli').val() !== '';

    $('button[name="submitButton"]').prop(
      'disabled',
      !(isSupplierSelected && isProductSelected && isQtyFilled && isDateFilled)
    );
  }
});
