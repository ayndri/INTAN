'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const previewTemplate = `
    <div class="dz-preview dz-file-preview">
      <div class="dz-details">
        <div class="dz-thumbnail">
          <img data-dz-thumbnail>
          <span class="dz-nopreview">No preview</span>
          <div class="dz-success-mark"></div>
          <div class="dz-error-mark"></div>
          <div class="dz-error-message"><span data-dz-errormessage></span></div>
          <div class="progress">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
          </div>
        </div>
        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
      </div>
    </div>`;

  const dropzoneElement = document.getElementById('dropzone-multi');
  const existingImages = JSON.parse(dropzoneElement.getAttribute('data-images'));

  Dropzone.autoDiscover = false;

  const myDropzone = new Dropzone('#dropzone-multi', {
    url: '/upload', // URL upload
    previewTemplate: previewTemplate,
    parallelUploads: 1,
    maxFilesize: 5, // Maksimal ukuran file dalam MB
    addRemoveLinks: true, // Tampilkan tombol hapus
    init: function () {
      const dropzoneInstance = this;

      // Loop melalui gambar yang sudah ada
      if (existingImages && existingImages.length > 0) {
        existingImages.forEach(image => {
          const mockFile = {
            name: image.image.split('/').pop(), // Nama file
            size: 12345, // Ukuran file (dummy jika tidak tersedia)
            dataURL: `/storage/${image.image}` // Path gambar
          };

          // Tambahkan file ke Dropzone
          dropzoneInstance.emit('addedfile', mockFile);
          dropzoneInstance.emit('thumbnail', mockFile, `/storage/${image.image}`);
          dropzoneInstance.emit('complete', mockFile);

          // Tanda file telah diunggah
          mockFile.status = Dropzone.SUCCESS;

          // Tambahkan data hidden untuk mengirim file ke server jika dihapus
          const inputElement = document.createElement('input');
          inputElement.type = 'hidden';
          inputElement.name = 'existing_images[]';
          inputElement.value = image.id;
          dropzoneElement.appendChild(inputElement);
        });
      }
    },
    removedfile: function (file) {
      if (file.previewElement) {
        file.previewElement.remove();
      }

      // Hapus input hidden untuk file yang dihapus
      if (file.status === Dropzone.SUCCESS && file.dataURL) {
        const imageIdInput = dropzoneElement.querySelector(`input[value="${file.id}"]`);
        if (imageIdInput) imageIdInput.remove();
      }
    }
  });
});
