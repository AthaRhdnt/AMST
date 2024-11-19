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
<!-- ImagePreview -->
<script>
    function preview(selector, temporaryFile, width = 200)  {
        $(selector).empty();
        $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
    }
</script>
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        let timeout = null; // Variable to hold the timeout

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // Clear the previous timeout
                clearTimeout(timeout);

                // Set a new timeout to trigger the form submission after a delay
                timeout = setTimeout(() => {
                    this.form.submit();
                }, 400); // 300 milliseconds delay (you can adjust this value)
            });
        }
    });
</script>
