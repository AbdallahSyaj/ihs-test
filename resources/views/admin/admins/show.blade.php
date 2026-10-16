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
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.admins-index') }}">{{ __('dashboard.shared.modules.admins') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.main_info') }}</li>
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
                    {{-- Edit --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.admins-edit', isset($admin->id) ? $admin->id : -1) }}"
                            class="btn btn-secondary">
                            <i data-feather="edit" class="fill-white feather-sm"></i> {{ __('dashboard.shared.edit') }}
                        </a>
                    </div>
                    {{-- Active / Inactive --}}
                    <div class="dropdown me-2">
                        @if (isset($admin->status) && $admin->status == 'Active')
                            <a href="{{ route('super_admin.admins-activeInactiveSingle', isset($admin->id) ? $admin->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="pause" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_inactive') }}
                            </a>
                        @elseif(isset($admin->status) && $admin->status == 'Inactive')
                            <a href="{{ route('super_admin.admins-activeInactiveSingle', isset($admin->id) ? $admin->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="play" class="fill-white feather-sm"></i> {{ __('dashboard.shared.set_active') }}
                            </a>
                        @endif
                    </div>
                    {{-- Delete --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.admins-softDelete', isset($admin->id) ? $admin->id : -1) }}"
                            class="confirm btn btn-danger">
                            <i data-feather="trash" class="fill-white feather-sm"></i> {{ __('dashboard.shared.delete') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ========================================================== --}}
    {{-- ==================== Page Body Section =================== --}}
    {{-- ========================================================== --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab_header_1" data-bs-toggle="pill" href="#tab_body_1"
                                role="tab" aria-controls="pills-profile" aria-selected="false"><strong>Main
                                    Info</strong></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        {{-- Tab One --}}
                        <div class="tab-pane fade show active" id="tab_body_1" role="tabpanel" aria-labelledby="tab_body_1">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table id="file_export_main_info_part_1"
                                                class="table table-striped table-bordered display">
                                                <thead>

                                                    {{-- name --}}
                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.name') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->name) ? $admin->name : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    {{-- email --}}
                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.email') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->email) ? $admin->email : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_time') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->created_at) ? date('h:i A', strtotime($admin->created_at)) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table id="file_export_main_info_part_1"
                                                class="table table-striped table-bordered display">
                                                <thead>
                                                    {{-- status --}}
                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.status') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->status) ? ($admin->status == 'Active' ? __('dashboard.shared.active') : __('dashboard.shared.inactive')) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_date') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->created_at) ? date('d/ F (m) / Y', strtotime($admin->created_at)) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.added_since') }}</th>
                                                        <td>
                                                            <strong>{!! isset($admin->created_at) ? $admin->created_at->diffForHumans() : '-------' !!}</strong>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="two-factor-reset">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4>{{ __('dashboard.two_factor.reset_title') }}</h4>
                        @if ((int) auth()->guard('super_admin')->id() === (int) ($admin->id ?? 0))
                            <p>{{ __('dashboard.two_factor.use_self_service') }}</p>
                            <a href="{{ route('super_admin.twoFactor-settings') }}" class="btn btn-secondary">{{ __('dashboard.two_factor.menu_label') }}</a>
                        @else
                            <p>{{ __('dashboard.two_factor.reset_help', ['email' => $admin->email ?? '']) }}</p>
                            <form method="POST" action="{{ route('super_admin.admins-resetTwoFactor', isset($admin->id) ? $admin->id : -1) }}"
                                onsubmit="return confirm(@json(__('dashboard.two_factor.reset_confirm')));">
                                @csrf
                                <div class="mb-3">
                                    <label for="confirm_email" class="form-label">{{ __('dashboard.two_factor.reset_email_label') }}</label>
                                    <input id="confirm_email" type="email" name="confirm_email" class="form-control" required autocomplete="off">
                                </div>
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.two_factor.reset_button') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
