$(document).ready(function () {
  // Initialize Select2 for dropdowns
  $('.select2').select2({
    placeholder: 'Select an option',
    allowClear: true
  });

  // Initialize Flatpickr for the purchase date input
  $('#purchaseDate').flatpickr({
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
  });

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
        $('#productPrice').val(formatRupiah(product.sell_price));
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
});

// Function to format numbers as Rupiah
function formatRupiah(value) {
  // Remove non-numeric characters
  const numberString = value.replace(/[^,\d]/g, '').toString();
  const split = numberString.split(',');

  // Add thousand separators
  let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  // If the decimal part exists and is not '00', append it, otherwise return just the integer part
  if (split[1] !== undefined && split[1] !== '00') {
    return rupiah + ',' + split[1];
  } else {
    return rupiah; // No decimal part or it's '00'
  }
}

$(document).ready(function () {
  const productTableBody = $('#productTableBody');
  const searchProductInput = $('#searchProduct');

  // Fetch products dynamically
  searchProductInput.on('input', function () {
    const query = $(this).val();

    if (query.length > 2) {
      // Minimum 3 characters to search
      $.ajax({
        url: '/api/products/search',
        method: 'GET',
        data: { q: query },
        success: function (data) {
          if (data.length) {
            showDropdown(data); // Show dropdown with product suggestions
          } else {
            hideDropdown();
          }
        },
        error: function () {
          console.error('Error fetching products.');
        }
      });
    } else {
      hideDropdown();
    }
  });

  // Function to show dropdown
  function showDropdown(products) {
    let dropdown = $('#productDropdown');

    if (!dropdown.length) {
      dropdown = $('<ul id="productDropdown" class="dropdown-menu"></ul>').css({
        position: 'absolute',
        zIndex: 1000,
        width: $('#searchProduct').outerWidth()
      });
      $('#searchProduct').after(dropdown);
    }

    // Dapatkan ID produk yang sudah ada di tabel
    const existingProductIds = [];
    $('#productTableBody tr').each(function () {
      const productId = $(this).data('id');
      if (productId) existingProductIds.push(productId);
    });

    dropdown.empty();
    products.forEach(product => {
      // Hanya tambahkan ke dropdown jika belum ada di tabel
      if (!existingProductIds.includes(product.id)) {
        let price = formatRupiah(product.sell_price);
        dropdown.append(
          `<li class="dropdown-item" data-id="${product.id}" data-name="${product.name}" data-price="${product.sell_price}" data-unit-cost="${product.unit_cost}">
          ${product.name}
        </li>`
        );
      }
    });

    dropdown.show();

    // Handle dropdown item click
    dropdown.find('.dropdown-item').on('click', function () {
      const product = {
        id: $(this).data('id'),
        name: $(this).data('name'),
        sell_price: $(this).data('price'),
        unit_cost: $(this).data('unit-cost')
      };

      addProductRow(product);
      $('#searchProduct').val(''); // Clear the search input
      hideDropdown(); // Hide dropdown
    });
  }

  // Function to hide dropdown
  function hideDropdown() {
    $('#productDropdown').hide();
  }

  function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function updateTotalCostInput(value) {
    const formattedValue = formatRupiah(value);
    document.querySelector('.total-cost-input').value = formattedValue;
  }

  // Menambahkan produk ke tabel
  function addProductRow(product) {
    // Calculate the index for the new row based on the current number of rows
    const index = $('#productTableBody tr').length;

    // Construct the row with properly named inputs
    const row = `
    <tr data-id="${product.id}">
      <td>${product.name}</td>
      <td>
        <div class="d-flex align-items-center">
          <button type="button" class="btn btn-sm btn-outline-primary minus-btn">-</button>
          <input type="number" name="products[${index}][quantity]" class="form-control mx-2 text-center quantity-input" value="1" min="1" style="width: 60px;">
          <button type="button" class="btn btn-sm btn-outline-primary plus-btn">+</button>
        </div>
      </td>
      <td>${formatRupiah(product.sell_price)}</td>
      <td>
        <input type="number" name="products[${index}][unit_cost]" class="form-control unit-cost-input" value="${
      product.unit_cost || 0
    }" min="0" placeholder="Unit Cost">
      </td>
      <td>
        <input type="number" name="products[${index}][total_cost]" class="form-control total-cost-input" value="0" readonly>
      </td>
      <td>
        <button type="button" class="btn btn-sm btn-danger remove-btn">Remove</button>
      </td>
      <input type="hidden" name="products[${index}][product_id]" value="${product.id}">
    </tr>
  `;

    // Append the row to the table body
    $('#productTableBody').append(row);

    // Calculate the total for the newly added row
    const lastRow = $('#productTableBody tr:last');
    calculateRowTotal(lastRow);

    // Attach event listeners for quantity and unit cost changes
    lastRow.find('.quantity-input, .unit-cost-input').on('input', function () {
      calculateRowTotal(lastRow);
    });
  }

  // Hitung total cost per baris
  function calculateRowTotal(row) {
    const quantity = parseInt(row.find('.quantity-input').val()) || 0;
    const unitCost = parseFloat(row.find('.unit-cost-input').val()) || 0;

    // Calculate the total cost for the row
    const totalCost = quantity * unitCost;

    // Update the total cost input in the row
    row.find('.total-cost-input').val(totalCost.toFixed(2));

    // Recalculate the grand total
    calculateGrandTotal();
  }

  // Hitung grand total
  function calculateGrandTotal() {
    let grandTotal = 0;

    // Iterate over each row and sum up the total costs
    $('#productTableBody tr').each(function () {
      const rowTotal = parseFloat($(this).find('.total-cost-input').val()) || 0;
      grandTotal += rowTotal;
    });

    // Include additional costs like tax, discount, and shipping
    const orderTax = parseFloat($('#orderTax').val()) || 0;
    const discount = parseFloat($('#discount').val()) || 0;
    const shipping = parseFloat($('#shipping').val()) || 0;

    grandTotal += orderTax + shipping - discount;

    // Update the grand total and other fields
    $('#grandTotal').text(formatRupiah(grandTotal));
    $('#displayShipping').text(formatRupiah(shipping));
    $('#displayDiscount').text(formatRupiah(discount));
    $('#displayOrderTax').text(formatRupiah(orderTax));
  }

  // Event Listener untuk perubahan input di tabel produk
  $('#productTableBody').on('input', '.quantity-input, .unit-cost-input', function () {
    const row = $(this).closest('tr');
    calculateRowTotal(row);
  });

  // // Event Listener untuk tombol + dan -
  // $('#productTableBody').on('click', '.plus-btn', function () {
  //   const input = $(this).closest('div').find('.quantity-input');
  //   input.val(parseInt(input.val()) + 1);
  //   const row = $(this).closest('tr');
  //   calculateRowTotal(row);
  // });

  // $('#productTableBody').on('click', '.minus-btn', function () {
  //   const input = $(this).closest('div').find('.quantity-input');
  //   if (parseInt(input.val()) > 1) {
  //     input.val(parseInt(input.val()) - 1);
  //     const row = $(this).closest('tr');
  //     calculateRowTotal(row);
  //   }
  // });

  // Event Listener untuk Remove Button
  $('#productTableBody').on('click', '.remove-btn', function () {
    $(this).closest('tr').remove();
    calculateGrandTotal();
  });

  // Event Listener untuk Order Tax, Discount, Shipping
  $('#orderTax, #discount, #shipping').on('input', calculateGrandTotal);

  // Handle dynamic row actions
  $(document).on('click', '.minus-btn', function () {
    const row = $(this).closest('tr');
    const input = row.find('.quantity-input');
    const newValue = Math.max(1, parseInt(input.val()) - 1);
    input.val(newValue);
    calculateRowTotal(row);
  });

  $(document).on('click', '.plus-btn', function () {
    const row = $(this).closest('tr');
    const input = row.find('.quantity-input');
    input.val(parseInt(input.val()) + 1);
    calculateRowTotal(row);
  });

  $(document).on('input', '.unit-cost-input, .discount-input, .tax-input', function () {
    const row = $(this).closest('tr');
    calculateRowTotal(row);
  });

  $(document).on('click', '.remove-btn', function () {
    $(this).closest('tr').remove();
  });

  // Hide dropdown when clicking outside
  $(document).on('click', function (e) {
    if (!$(e.target).closest('#searchProduct, #productDropdown').length) {
      hideDropdown();
    }
  });

  $('#formValidationExamples').on('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    const form = $(this);
    const formData = new FormData(this); // Collect form data

    $.ajax({
      url: form.attr('action'), // Use the form's action attribute
      type: 'POST',
      data: formData,
      processData: false, // Disable automatic processing
      contentType: false, // Set to false for FormData
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
      },
      success: function (response) {
        // Show success message with SweetAlert
        Swal.fire({
          icon: 'success',
          title: 'Purchase Created!',
          text: 'The purchase has been successfully created.',
          customClass: { confirmButton: 'btn btn-success' }
        }).then(() => {
          window.location.href = 'http://intan-web.test/purchases';
        });

        // Optional: Reset form after success (if not redirecting immediately)
        form[0].reset();
      },
      error: function (err) {
        // Show error message with SweetAlert
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: err.responseJSON?.message || 'An error occurred while creating the purchase.',
          customClass: { confirmButton: 'btn btn-danger' }
        });
      }
    });
  });
});
