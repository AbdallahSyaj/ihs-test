@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.privacy_policy') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.privacy_policies-index') }}">{{ __('dashboard.shared.modules.privacy_policy') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.main_info') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    {{-- Create --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.privacy_policies-create') }}" class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i>{{ __('dashboard.shared.create_new') }}
                        </a>
                    </div>
                    {{-- Edit --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.privacy_policies-edit', isset($privacyPolicy->id) ? $privacyPolicy->id : -1) }}"
                            class="btn btn-secondary">
                            <i data-feather="edit" class="fill-white feather-sm"></i> {{ __('dashboard.shared.edit') }}
                        </a>
                    </div>
                    {{-- Active / Inactive --}}
                    <div class="dropdown me-2">
                        @if (isset($privacyPolicy->status) && $privacyPolicy->status == 'Active')
                            <a href="{{ route('super_admin.privacy_policies-activeInactiveSingle', isset($privacyPolicy->id) ? $privacyPolicy->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="pause" class="fill-white feather-sm"></i> {{ __('dashboard.shared.set_inactive') }}
                            </a>
                        @elseif(isset($privacyPolicy->status) && $privacyPolicy->status == 'Inactive')
                            <a href="{{ route('super_admin.privacy_policies-activeInactiveSingle', isset($privacyPolicy->id) ? $privacyPolicy->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="play" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_active') }}
                            </a>
                        @endif
                    </div>
                    {{-- Delete --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.privacy_policies-softDelete', isset($privacyPolicy->id) ? $privacyPolicy->id : -1) }}"
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
        <div class="card">
            @if (isset($privacyPolicy) && $privacyPolicy->count() > 0)
                <div class="card-body">
                    <div class="row">
                        @if (isset($privacyPolicy))
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="file_export_main_info_part_1"
                                        class="table table-striped table-bordered display">
                                        <thead>
                                            {{-- title_ar --}}
                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.title_ar') }}</th>
                                                <td>
                                                    <strong>{!! isset($privacyPolicy->title_ar) ? $privacyPolicy->title_ar : '-------' !!}</strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_time') }}</th>
                                                <td>
                                                    <strong>{!! isset($privacyPolicy->created_at) ? date('h:i A', strtotime($privacyPolicy->created_at)) : '-------' !!}</strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_date') }}</th>
                                                <td>
                                                    <strong>{!! isset($privacyPolicy->created_at) ? date('d/ F (m) / Y', strtotime($privacyPolicy->created_at)) : '-------' !!}</strong>
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
                                            {{-- title_en --}}
                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.title_en') }}</th>
                                                <td>
                                                    <strong>{!! isset($privacyPolicy->title_en) ? $privacyPolicy->title_en : '-------' !!}</strong>
                                                </td>
                                            </tr>

                                            {{-- status --}}
                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.status') }}</th>
                                                <td>
                                                    @if ($privacyPolicy->status == 'Active')
                                                        <span style="color:green;"><strong>{{ __('dashboard.shared.active') }}
                                                            </strong></span>
                                                    @elseif($privacyPolicy->status == 'Inactive')
                                                        <span style="color:red;"><strong>{{ __('dashboard.shared.inactive') }}
                                                            </strong></span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th style="background-color:aliceblue">{{ __('dashboard.shared.added_since') }}</th>
                                                <td>
                                                    <strong>{!! isset($privacyPolicy->created_at) ? $privacyPolicy->created_at->diffForHumans() : '-------' !!}</strong>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- description_ar --}}
                            <div class="col-md-12">
                                @if (isset($privacyPolicy->description_ar))
                                    <div class="card description-card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ __('dashboard.shared.description_ar') }}</h5>
                                            <p class="card-text">{!! isset($privacyPolicy->description_ar) ? $privacyPolicy->description_ar : '-------' !!}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- description_en --}}
                            <div class="col-md-12">
                                @if (isset($privacyPolicy->description_en))
                                    <div class="card description-card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ __('dashboard.shared.description_en') }}</h5>
                                            <p class="card-text">{!! isset($privacyPolicy->description_en) ? $privacyPolicy->description_en : '-------' !!}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
