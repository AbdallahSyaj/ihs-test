@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.sliders') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.sliders-index') }}">{{ __('dashboard.shared.modules.sliders') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.main_info') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    {{-- Create --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-create') }}" class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i>{{ __('dashboard.shared.create_new') }}
                        </a>
                    </div>
                    {{-- Edit --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-edit', isset($slider->id) ? $slider->id : -1) }}"
                            class="btn btn-secondary">
                            <i data-feather="edit" class="fill-white feather-sm"></i> {{ __('dashboard.shared.edit') }}
                        </a>
                    </div>
                    {{-- Active / Inactive --}}
                    <div class="dropdown me-2">
                        @if (isset($slider->status) && $slider->status == 'Active')
                            <a href="{{ route('super_admin.sliders-activeInactiveSingle', isset($slider->id) ? $slider->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="pause" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_inactive') }}
                            </a>
                        @elseif(isset($slider->status) && $slider->status == 'Inactive')
                            <a href="{{ route('super_admin.sliders-activeInactiveSingle', isset($slider->id) ? $slider->id : -1) }}"
                                class="process btn btn-warning">
                                <i data-feather="play" class="fill-white feather-sm"></i> {{ __('dashboard.shared.set_active') }}
                            </a>
                        @endif
                    </div>
                    {{-- Delete --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-softDelete', isset($slider->id) ? $slider->id : -1) }}"
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
                                role="tab" aria-controls="pills-profile" aria-selected="false"><strong>{{ __('dashboard.shared.main_info') }}</strong></a>
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
                                                    {{-- title_ar --}}
                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.title_ar') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->title_ar) ? $slider->title_ar : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_time') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->created_at) ? date('h:i A', strtotime($slider->created_at)) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.addition_date') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->created_at) ? date('d/ F (m) / Y', strtotime($slider->created_at)) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    {{-- type --}}
                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.type') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->type) ? $slider->type : '-------' !!}</strong>
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
                                                            <strong>{!! isset($slider->title_en) ? $slider->title_en : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.status') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->status) ? ($slider->status == 'Active' ? __('dashboard.shared.active') : __('dashboard.shared.inactive')) : '-------' !!}</strong>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th style="background-color:aliceblue">{{ __('dashboard.shared.added_since') }}</th>
                                                        <td>
                                                            <strong>{!! isset($slider->created_at) ? $slider->created_at->diffForHumans() : '-------' !!}</strong>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>


                                    {{-- description_ar --}}
                                    <div class="col-md-12">
                                        @if (isset($slider->description_ar))
                                            <div class="card description-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('dashboard.shared.description_ar') }}:</h5>
                                                    <p class="card-text">{!! isset($slider->description_ar) ? $slider->description_ar : '-------' !!}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- description_en --}}
                                    <div class="col-md-12">
                                        @if (isset($slider->description_en))
                                            <div class="card description-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('dashboard.shared.description_en') }}:</h5>
                                                    <p class="card-text">{!! isset($slider->description_en) ? $slider->description_en : '-------' !!}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>


                                    <div class="col-md-12">
                                        <div class="card mb-12 shadow-sm groove-container">
                                            <div class="card-body">
                                                <div>
                                                    @if (isset($slider) && $slider->image && file_exists($slider->image))
                                                        <img src="{{ asset($slider->image) }}" alt="Image" muted controls
                                                            height="400" width="800">
                                                    @else
                                                        <img src="{{ asset('style_files/images/notfound.png') }}"
                                                            alt="Image" class="img-thumbnail image-preview">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
