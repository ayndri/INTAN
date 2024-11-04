$(document).ready(function () {
  // Initialize Select2 for dropdowns
  $('.select2').select2({
    placeholder: 'Select an option',
    allowClear: true
  });

  // Initialize Flatpickr with correct date format
  $('#tanggalBeli').flatpickr({
    enableTime: true,
    dateFormat: 'Y-m-d H:i:S', // Matches typical Laravel date format (Y-m-d H:i:S)
    altFormat: 'F j, Y H:i', // Display format, if needed
    altInput: true,
    defaultDate: $('#tanggalBeli').data('default') || null, // Pre-populate if editing
    onChange: function (selectedDates, dateStr, instance) {
      // Ensure the date is updated in the hidden field for submission
      $('#tanggalBeli').val(dateStr);
    }
  });

  // Load supplier and product options via AJAX
  loadOptions('/purchases/suppliers', '#formValidationSupplier', $('#formValidationSupplier').data('selected'));
  loadOptions('/purchases/products', '#formValidationProduct', $('#formValidationProduct').data('selected'));

  // Format initial fields with Rupiah formatting
  formatInitialRupiahFields();

  // Handle supplier selection
  $('#formValidationSupplier').on('change', function () {
    const supplierId = $(this).val();
    if (supplierId) {
      fetchSupplierDetails(supplierId);
      $('#formValidationProduct').prop('disabled', false);
    } else {
      clearSupplierDetails();
    }
  });

  // Handle product selection
  $('#formValidationProduct').on('change', function () {
    const productId = $(this).val();
    if (productId) {
      fetchProductDetails(productId);
      $('#qty, #tanggalBeli').prop('readonly', false).prop('disabled', false);
    } else {
      clearProductDetails();
    }
  });

  // Function to load options via AJAX
  function loadOptions(url, selectId, selectedId = null) {
    $.ajax({
      url: url,
      method: 'GET',
      success: function (data) {
        const select = $(selectId);
        select.empty().append('<option value="" disabled>Select an option</option>');
        data.forEach(item => {
          const isSelected = item.id == selectedId ? 'selected' : '';
          select.append(`<option value="${item.id}" ${isSelected}>${item.name}</option>`);
        });
      },
      error: function () {
        alert('Failed to load options');
      }
    });
  }

  // Format number as Rupiah
  function formatRupiah(amount) {
    return (
      'Rp ' +
      Number(amount).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      })
    );
  }

  // Function to format initial Rupiah fields on page load
  function formatInitialRupiahFields() {
    $('#productPrice, #productCost, #priceSt, #total').each(function () {
      const value = $(this)
        .val()
        .replace(/[Rp .]/g, '')
        .replace(',', '.');
      if (!isNaN(parseFloat(value))) {
        $(this).val(formatRupiah(value));
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
      $('#total').val(formatRupiah(total));
    } else {
      $('#total').val('');
    }
  }

  // Prepopulate supplier and product details if in edit mode
  const supplierId = $('#formValidationSupplier').data('selected');
  const productId = $('#formValidationProduct').data('selected');
  if (supplierId) fetchSupplierDetails(supplierId);
  if (productId) fetchProductDetails(productId);
});
