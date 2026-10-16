@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.admins') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.admins-index') }}">{{ __('dashboard.shared.modules.admins') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.archive') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    {{-- Create --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.admins-create') }}" class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i>{{ __('dashboard.shared.create_new') }}
                        </a>
                    </div>
                    @if (isset($admins) && $admins->count() > 0)
                        {{-- Setting --}}
                        <div class="dropdown me-2">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('dashboard.shared.settings') }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><button id="softDeleteRestoreSelected" class="dropdown-item"
                                        onclick="softDeleteRestoreSelected()">{{ __('dashboard.shared.restore_selected') }}</button>
                                </li>
                            </ul>
                        </div>
                        {{-- Select/Deselect all --}}
                        <div class="dropdown me-2">
                            <button class="toggleSelectAllButton btn btn-primary" onclick="selectDeselectAll()">
                                {{ __('dashboard.shared.select_deselect_all') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- ==================== Page Body Section =================== --}}
    {{-- ========================================================== --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="file_export" class="table table-striped table-bordered display">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.shared.name') }}</th>
                                        <th>{{ __('dashboard.shared.status') }}</th>
                                        <th>{{ __('dashboard.shared.date_time') }}</th>
                                        <th>{{ __('dashboard.shared.control') }}</th>
                                        <th>{{ __('dashboard.shared.select') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($admins) && $admins->count() > 0)
                                        @foreach ($admins as $admin)
                                            <tr>
                                                <td>{{ isset($admin->name) ? $admin->name : '----' }}</td>

                                                <th>
                                                    @if ($admin->status == 'Active')
                                                        <span
                                                            style="color:green;">{{ __('dashboard.shared.active') }}</span>
                                                    @elseif($admin->status == 'Inactive')
                                                        <span
                                                            style="color:red;">{{ __('dashboard.shared.inactive') }}</span>
                                                    @endif
                                                </th>

                                                <td>{!! isset($admin->created_at)
                                                    ? '<strong>' . __('dashboard.shared.date_label') . ' : </strong>' .
                                                        date('Y -m-d', strtotime($admin->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.time_label') . ' : </strong>' .
                                                        date('h:i A', strtotime($admin->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.since_label') . ' : </strong> ' .
                                                        $admin->created_at->diffForHumans()
                                                    : "<span style='color:blue;'>----------</span>" !!}
                                                </td>

                                                <td>
                                                    <div class="button-group">
                                                        <a href="{{ route('super_admin.admins-softDeleteRestore', [isset($admin->id) ? $admin->id : -1]) }}"
                                                            class="unarchive btn waves-effect waves-light btn-success btn-sm"
                                                            title="{{ __('dashboard.shared.restore_this_record') }}"><i
                                                                class="mdi mdi-redo-variant"></i></a>
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <input type="checkbox" class="selectedAdmins" name="selectedAdmins[]"
                                                        value="{{ $admin->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    {{-- Select/Deselect all --}}
    <script>
        function selectDeselectAll() {
            // Get bcheckbox using CSS class classes
            var selectedAdmins = document.querySelectorAll(".selectedAdmins");
            // Determine whether the boxes are selected or not
            var areAllChecked = true;
            for (var i = 0; i < selectedAdmins.length; i++) {
                if (!selectedAdmins[i].checked) {
                    areAllChecked = false;
                    break;
                }
            }
            // Change the status of the check box based on the current status
            for (var i = 0; i < selectedAdmins.length; i++) {
                selectedAdmins[i].checked = !areAllChecked;
            }
        }
    </script>

    {{-- Soft Delete Restore Selected --}}
    <script>
        function softDeleteRestoreSelected() {
            // Collect the selected rows
            var selectedAdmins = [];
            $('input[name="selectedAdmins[]"]:checked').each(function() {
                selectedAdmins.push($(this).val());
            });
            //If rows are selected, you can perform the function here
            if (selectedAdmins.length > 0) {
                if (!confirm('Are you sure about canceling the archive?')) {
                    return;
                }
                //Prepare the data as a query
                var query = '?selectedAdmins=' + selectedAdmins.join(',');
                // Create the link with the query
                var link = '{{ route('super_admin.admins-softDeleteRestoreSelected') }}' + query;
                // Direct the browser to the link after preparing it
                window.location.href = link;
            } else {
                Swal.fire(
                    '{{ __('dashboard.shared.oops_title') }}',
                    '{{ __('dashboard.shared.select_at_least_one') }}',
                    'error'
                )
            }
        }
    </script>
@endsection
