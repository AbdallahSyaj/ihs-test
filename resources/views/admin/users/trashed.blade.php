@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.users') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.users-index') }}">{{ __('dashboard.shared.modules.users') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.archive') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    {{-- Create --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.users-create') }}" class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i>{{ __('dashboard.shared.create_new') }}
                        </a>
                    </div>
                    @if (isset($users) && $users->count() > 0)
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
                                        <th>{{ __('dashboard.shared.first_name') }}</th>
                                        <th>{{ __('dashboard.shared.last_name') }}</th>
                                        <th>{{ __('dashboard.shared.status') }}</th>
                                        <th>{{ __('dashboard.shared.date_time') }}</th>
                                        <th>{{ __('dashboard.shared.control') }}</th>
                                        <th>{{ __('dashboard.shared.select') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($users) && $users->count() > 0)
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ isset($user->first_name) ? $user->first_name : '----' }}</td>
                                                <td>{{ isset($user->last_name) ? $user->last_name : '----' }}</td>

                                                <th>
                                                    @if ($user->status == 'Active')
                                                        <span
                                                            style="color:green;">{{ __('dashboard.shared.active') }}</span>
                                                    @elseif($user->status == 'Inactive')
                                                        <span
                                                            style="color:red;">{{ __('dashboard.shared.inactive') }}</span>
                                                    @endif
                                                </th>

                                                <td>{!! isset($user->created_at)
                                                    ? '<strong>' . __('dashboard.shared.date_label') . ' : </strong>' .
                                                        date('Y -m-d', strtotime($user->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.time_label') . ' : </strong>' .
                                                        date('h:i A', strtotime($user->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.since_label') . ' : </strong> ' .
                                                        $user->created_at->diffForHumans()
                                                    : "<span style='color:blue;'>----------</span>" !!}
                                                </td>

                                                <td>
                                                    <div class="button-group">
                                                        <a href="{{ route('super_admin.users-softDeleteRestore', [isset($user->id) ? $user->id : -1]) }}"
                                                            class="unarchive btn waves-effect waves-light btn-success btn-sm"
                                                            title="{{ __('dashboard.shared.restore_this_record') }}"><i
                                                                class="mdi mdi-redo-variant"></i></a>
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <input type="checkbox" class="selectedUsers" name="selectedUsers[]"
                                                        value="{{ $user->id }}">
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
            var selectedUsers = document.querySelectorAll(".selectedUsers");
            // Determine whether the boxes are selected or not
            var areAllChecked = true;
            for (var i = 0; i < selectedUsers.length; i++) {
                if (!selectedUsers[i].checked) {
                    areAllChecked = false;
                    break;
                }
            }
            // Change the status of the check box based on the current status
            for (var i = 0; i < selectedUsers.length; i++) {
                selectedUsers[i].checked = !areAllChecked;
            }
        }
    </script>

    {{-- Soft Delete Restore Selected --}}
    <script>
        function softDeleteRestoreSelected() {
            // Collect the selected rows
            var selectedUsers = [];
            $('input[name="selectedUsers[]"]:checked').each(function() {
                selectedUsers.push($(this).val());
            });
            //If rows are selected, you can perform the function here
            if (selectedUsers.length > 0) {
                if (!confirm('Are you sure about canceling the archive?')) {
                    return;
                }
                //Prepare the data as a query
                var query = '?selectedUsers=' + selectedUsers.join(',');
                // Create the link with the query
                var link = '{{ route('super_admin.users-softDeleteRestoreSelected') }}' + query;
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
