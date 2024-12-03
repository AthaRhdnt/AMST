<!-- jQuery -->
<script src="{{ asset('/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('/plugins/moment/moment.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('/js/adminlte.js') }}"></script>
{{-- <script src="{{ asset('/js/custom.js') }}"></script> --}}
<!-- DataTables -->
{{-- <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script> --}}
<!-- SweetAlert2 -->
<script src="{{ asset('/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Validator -->
<script src="{{ asset('js/validator.min.js') }}"></script>
<!-- Cropper -->
<script src="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.js"></script>
<!-- HeaderDropdownMenu -->
<script>
    $(document).ready(function() {
        let timeout;
        $('.user-menu').on('mouseenter', function() {
            clearTimeout(timeout); // Clear any existing timeout
            $(this).find('.dropdown-menu').stop(true, true).slideDown(200);
        });
        $('.user-menu, .dropdown-menu').on('mouseleave', function() {
            const $dropdownMenu = $(this).find('.dropdown-menu');
            timeout = setTimeout(function() {
                $dropdownMenu.stop(true, true).slideUp(200);
            }, 200); 
        });
        $('.dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
<!-- AutoSearch -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');

        if (searchInput) {
            // Automatically focus the search input field
            searchInput.focus();
            const value = searchInput.value;
            searchInput.setSelectionRange(value.length, value.length);

            // Re-attach debounce behavior for form submission
            let timeout = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.form.submit();
                }, 400); // Adjust the delay as needed
            });
        }
    });
</script>
<!-- Time -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateTimeDisplay = document.getElementById('dateTimeDisplay');

        function updateDateTime() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0'); // 24-hour format
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0'); // Include seconds if needed
            dateTimeDisplay.textContent = `${day}-${month}-${year} ${hours}:${minutes}`;
        }

        function alignWithClock() {
            updateDateTime(); // Update immediately
            const now = new Date();
            const millisecondsUntilNextMinute = 60000 - now.getSeconds() * 1000 - now.getMilliseconds();
            setTimeout(() => {
                updateDateTime(); // Sync exactly at the next minute
                setInterval(updateDateTime, 60000); // Continue updating every minute
            }, millisecondsUntilNextMinute);
        }

        alignWithClock(); // Start the process
    });
</script>
<!--  Datepicker Shortcut  -->
<script>
    $(function() {
        $('#date-range').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment(), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            }
        });

        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
            $('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
            $(this).closest('form').submit(); // Trigger form submit
        });

        $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
            $('input[name="start_date"]').val('');
            $('input[name="end_date"]').val('');
            $(this).closest('form').submit(); // Trigger form submit
        });
    });

    function setDateRange(range) {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        const today = new Date();
        let startDate;
        let endDate;

        switch (range) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                break;
            case 'this_year':
                startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                break;
            case 'last_7_days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 6);
                startDate = startDate.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_30_days':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 29);
                startDate = startDate.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            default:
                return;
        }

        startDateInput.value = startDate;
        endDateInput.value = endDate;
        
        // Construct the new URL with query parameters
        const form = startDateInput.closest('form');
        const url = new URL(form.action); // Get the form action URL

        // Append the date range to the URL
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);

        // Remove existing search and entries parameters if needed
        url.searchParams.delete('search');
        url.searchParams.delete('entries');

        // Redirect to the new URL
        window.location.href = url.toString();
    }
</script>
<!-- Image Preview -->
{{-- <script>
    // Function to preview the selected image
    function previewImage(event) {
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const file = event.target.files[0];

        // If file is selected, show the preview
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.classList.remove('d-none'); // Make the image visible
            };

            reader.readAsDataURL(file);
        } else {
            // Reset preview if no file is selected
            previewImage.src = '';
            previewImage.classList.add('d-none');
        }
    }

    // Function to remove the image preview and reset the file input
    function removeImage() {
        const previewImage = document.getElementById('preview-image');
        const imageInput = document.getElementById('image');
        const currentImage = document.getElementById('current-image');

        // Reset the file input and image preview
        imageInput.value = '';  // Remove the selected file
        previewImage.src = '';  // Clear the preview image
        previewImage.classList.add('d-none');  // Hide the preview image

        if (currentImage) {
            currentImage.classList.add('d-none');  // Hide current image if it's being removed
        }
    }

    // If an image already exists, make sure the remove functionality is available on page load
    document.addEventListener('DOMContentLoaded', function () {
        const currentImage = document.getElementById('current-image');
        if (currentImage) {
            currentImage.style.cursor = 'pointer'; // Add pointer cursor to show it's clickable
        }
    });
</script> --}}
{{-- <script>
    let cropper = null; // Declare cropper outside the function to keep track of the instance

    // Function to preview and crop the selected image
    function previewImage(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('preview-image');
        const modalCrop = new bootstrap.Modal(document.getElementById('modalCrop')); // Initialize the modal
        const cropperImage = document.getElementById('cropper-image');
        const cropButton = document.getElementById('cropButton');
        const cancelButton = document.getElementById('cancelButton');
        const removeExistingInput = document.getElementById('remove_existing_image'); // Hidden input for flag

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                cropperImage.src = e.target.result; // Load the image into the cropper
                modalCrop.show(); // Open the modal after the image is loaded

                cropperImage.onload = function () {

                    // Destroy the previous cropper instance if it exists
                    if (cropper) {
                        cropper.destroy();
                        cropper = null; // Reset the cropper reference
                    }

                    // Delay the modal reflow to allow for rendering
                    setTimeout(() => {
                        const modalDialog = document.querySelector('#modalCrop .modal-dialog');

                        // Now trigger the reflow to update modal size after the content is rendered
                        const reflowHeight = modalDialog.offsetHeight;

                        // Now we can be sure the modal content is sized correctly based on the image
                        cropperImage.style.maxWidth = '100%';   // Ensure the image takes full width of the modal
                        cropperImage.style.maxHeight = '80vh';  // Limit the image height to 80% of the viewport height


                        // Initialize Cropper.js here
                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 4 / 3, // Set desired aspect ratio
                            viewMode: 2,
                            autoCropArea: 0.65,
                            responsive: true,
                        });

                        // Crop button logic
                        cropButton.onclick = function () {

                            const canvas = cropper.getCroppedCanvas({
                                width: 200,
                                height: 150,
                            });
                            const croppedImage = canvas.toDataURL('image/jpeg');
                            previewImage.src = croppedImage;
                            previewImage.classList.remove('d-none');
                            modalCrop.hide();
                            cropper.destroy(); // Cleanup Cropper.js instance
                            cropper = null; // Reset cropper reference
                            if (currentImage) {
                                currentImage.classList.add('d-none'); // Hide the current image
                                if (removeExistingInput) {
                                    removeExistingInput.value = '1'; // Mark the existing image for removal
                                }
                            }
                        };

                        // Cancel button logic
                        cancelButton.onclick = function () {
                            modalCrop.hide();
                            cropper.destroy();
                            cropper = null; // Reset cropper reference
                        };
                    }, 50); // Add a slight delay before triggering the reflow (100ms)
                };
            };

            reader.readAsDataURL(file); // Read the file as a Data URL
        }
    }
    // Function to remove the image preview and reset the file input
    function removeImage() {
        const previewImage = document.getElementById('preview-image');
        const currentImage = document.getElementById('current-image');
        const imageInput = document.getElementById('image');
        const removeExistingInput = document.getElementById('remove_existing_image'); // Hidden input for flag

        // Reset the file input and image preview
        imageInput.value = ''; // Remove the selected file
        previewImage.src = ''; // Clear the preview image
        previewImage.classList.add('d-none'); // Hide the preview image

        if (currentImage) {
            currentImage.classList.remove('d-none'); // Show the current image again
            if (removeExistingInput) {
                removeExistingInput.value = '0'; // Unmark the existing image for removal
            }
        }
    }
</script> --}}
<script>
    let cropper = null; // Declare cropper outside the function to keep track of the instance

    // Function to preview and crop the selected image
    function previewImage(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('preview-image');
        const modalCrop = new bootstrap.Modal(document.getElementById('modalCrop')); // Initialize the modal
        const cropperImage = document.getElementById('cropper-image');
        const cropButton = document.getElementById('cropButton');
        const cancelButton = document.getElementById('cancelButton');
        const currentImage = document.getElementById('current-image'); // Current existing image
        const removeExistingInput = document.getElementById('remove_existing_image'); // Hidden input for flag

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                cropperImage.src = e.target.result; // Load the image into the cropper
                modalCrop.show(); // Open the modal after the image is loaded

                cropperImage.onload = function () {
                    // Destroy the previous cropper instance if it exists
                    if (cropper) {
                        cropper.destroy();
                        cropper = null; // Reset the cropper reference
                    }

                    // Delay the modal reflow to allow for rendering
                    setTimeout(() => {
                        // Initialize Cropper.js
                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 4 / 3, // Set desired aspect ratio
                            viewMode: 2,
                            autoCropArea: 0.65,
                            responsive: true,
                        });

                        // Crop button logic
                        cropButton.onclick = function () {
                            const canvas = cropper.getCroppedCanvas({
                                width: 200,
                                height: 150,
                            });
                            const croppedImage = canvas.toDataURL('image/jpeg');
                            previewImage.src = croppedImage;
                            previewImage.classList.remove('d-none'); // Show the preview image
                            modalCrop.hide(); // Hide the modal

                            // Hide the current image if it exists
                            if (currentImage) {
                                currentImage.classList.add('d-none');
                                if (removeExistingInput) {
                                    removeExistingInput.value = '1'; // Mark the existing image for removal
                                }
                            }

                            cropper.destroy(); // Cleanup Cropper.js instance
                            cropper = null; // Reset cropper reference
                        };

                        // Cancel button logic
                        cancelButton.onclick = function () {
                            modalCrop.hide();
                            cropper.destroy();
                            cropper = null; // Reset cropper reference
                        };
                    }, 50); // Add a slight delay before triggering the reflow
                };
            };

            reader.readAsDataURL(file); // Read the file as a Data URL
        }
    }

    // Function to remove the image preview and reset the file input
    function removeImage() {
        const previewImage = document.getElementById('preview-image');
        const currentImage = document.getElementById('current-image');
        const imageInput = document.getElementById('image');
        const removeExistingInput = document.getElementById('remove_existing_image'); // Hidden input for flag

        // Reset the file input and image preview
        imageInput.value = ''; // Remove the selected file
        previewImage.src = ''; // Clear the preview image
        previewImage.classList.add('d-none'); // Hide the preview image

        // Show the current image again and unmark removal
        if (currentImage) {
            currentImage.classList.remove('d-none');
            if (removeExistingInput) {
                removeExistingInput.value = '0'; // Unmark the existing image for removal
            }
        }
    }
</script>
<script>
    function openPreviewModal(id_transaksi) {
        // Fetch the print preview content from the correct route using the id_transaksi
        fetch(`/laporan/${id_transaksi}/preview`)
            .then(response => response.text())
            .then(html => {
                // Inject the content into the modal
                document.getElementById('previewContent').innerHTML = html;

                // Set the correct transaksiId to the print button
                document.getElementById('printBtn').setAttribute('data-transaksi-id', id_transaksi);

                // Open the modal to show the preview
                $('#previewModal').modal('show');
            })
            .catch(error => {
                console.error('Error loading print preview:', error);
            });
    }

    document.getElementById('printBtn').addEventListener('click', function() {
        // Get the transaksi ID from the button's data attribute
        var transaksiId = this.getAttribute('data-transaksi-id');

        // Construct the URL for the route
        var printUrl = `/laporan/${transaksiId}/print`;

        // // Open the PDF file in a new tab or directly download it
        // window.location.href = printUrl;

        // // Close the modal after initiating the print action
        // $('#previewModal').modal('hide');
        var printWindow = window.open(printUrl, '_blank');

        // Listen for the print window to close
        var checkWindowClosed = setInterval(function () {
            if (printWindow.closed) {
                clearInterval(checkWindowClosed);
                location.reload(); // Reload the page once the print window is closed
            }
        }, 1000);
    });
</script>