<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{ asset('style_files/backend/src/assets/libs/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('style_files/backend/src/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<!-- apps -->
<script src="{{ asset('style_files/backend/dist/js/app.min.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/app.init.horizontal.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/app-style-switcher.horizontal.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ asset('style_files/backend/src/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}">
</script>
<script src="{{ asset('style_files/backend/src/assets/libs/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('style_files/backend/dist/js/waves.js') }}"></script>
<!--Menu sidebar -->
<script src="{{ asset('style_files/backend/dist/js/sidebarmenu.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{ asset('style_files/backend/dist/js/feather.min.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/custom.min.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/custom_lal.js') }}"></script>
<!-- ############################################################### -->
<!-- This Page Js Files Here -->
<!-- ############################################################### -->
{{-- Locale-aware strings for style_files/shared/js/custom.js and DataTables --}}
<script>
    window.dashboardTranslations = {
        confirmDelete: @json(__('dashboard.shared.confirm_delete_message')),
        datatable: {
            search: @json(__('dashboard.datatable.search')),
            info: @json(__('dashboard.datatable.info')),
            infoEmpty: @json(__('dashboard.datatable.info_empty')),
            infoFiltered: @json(__('dashboard.datatable.info_filtered')),
            zeroRecords: @json(__('dashboard.datatable.zero_records')),
            emptyTable: @json(__('dashboard.datatable.zero_records')),
            paginate: {
                first: @json(__('dashboard.datatable.first')),
                last: @json(__('dashboard.datatable.last')),
                next: @json(__('dashboard.datatable.next')),
                previous: @json(__('dashboard.datatable.previous'))
            }
        }
    };
</script>

<!-- start - This is for export functionality only -->
<script src="{{ asset('style_files/backend/src/assets/libs/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/pages/datatable/custom-datatable.js') }}"></script>
{{-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script> --}}
<script src="{{ asset('style_files/backend/dist/js/pages/datatable/datatable-advanced.init.js') }}"></script>
<script src="{{ asset('style_files/backend/src/assets/extra-libs/tiny-editable/mindmup-editabletable.js') }}"></script>
<script src="{{ asset('style_files/backend/src/assets/extra-libs/tiny-editable/numeric-input-example.js') }}"></script>
{{-- <script>
        $('#mainTable').editableTableWidget().numericInputExample().find('td:first').focus();
        $('#editable-datatable').editableTableWidget().numericInputExample().find('td:first').focus();
        $(function () {
            $('#editable-datatable').DataTable();
        });
    </script> --}}

<script>
    $(document).ready(function() {
        // Initialize editableTableWidget for #mainTable
        $('#mainTable').editableTableWidget();

        // Initialize numericInputExample for #mainTable
        $('#mainTable').numericInputExample();

        // Focus on the first 'td' in #mainTable
        $('#mainTable').find('td:first').focus();
    });
</script>


{{-- Sweet Alert --}}
<script src="{{ asset('style_files/backend/src/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('style_files/backend/src/assets/extra-libs/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/custom.js') }}"></script>
<script src="{{ asset('style_files/shared/js/custom.js') }}"></script>

{{-- =========================================================== --}}
{{-- ================== Sweet Alert Section ==================== --}}
{{-- =========================================================== --}}
@if (session()->has('success'))
    <script>
        Swal.fire(
            "{{ __('dashboard.shared.success_title') }}",
            "{!! Session::get('success') !!}",
            "success",
        );
    </script>
@endif
@if (session()->has('danger'))
    <script>
        Swal.fire(
            "{{ __('dashboard.shared.error_title') }}",
            "{!! Session::get('danger') !!}",
            "error",
        );
    </script>
@endif

{{-- Miltiple Select --}}
<script src="{{ asset('style_files/backend/src/assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('style_files/backend/src/assets/libs/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('style_files/backend/dist/js/pages/forms/select2/select2.init.js') }}"></script>

{{-- Custom Statistics --}}
{{-- <script src="{{ asset('style_files/backend/dist/js/pages/dashboards/custom_statistics.js') }}"></script> --}}

@yield('extra_js')
</body>

</html>
