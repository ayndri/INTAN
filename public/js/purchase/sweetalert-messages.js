document.addEventListener('DOMContentLoaded', function () {
  const successMessage = document.getElementById('success-message');
  const errorMessage = document.getElementById('error-message');

  if (successMessage) {
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: successMessage.innerText,
      customClass: {
        confirmButton: 'btn btn-success'
      }
    });
  }

  if (errorMessage) {
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: errorMessage.innerText,
      customClass: {
        confirmButton: 'btn btn-danger'
      }
    });
  }
});
