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
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('super_admin.sliders-index') }}">{{ __('dashboard.shared.modules.sliders') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.update_info') }}</li>
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
                    {{-- Show --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-show', isset($slider->id) ? $slider->id : -1) }}"
                            class="btn btn-primary">
                            <i data-feather="eye" class="fill-white feather-sm"></i>{{ __('dashboard.shared.view') }}
                        </a>
                    </div>
                    {{-- Active / Inactive --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-activeInactiveSingle', isset($slider->id) ? $slider->id : -1) }}"
                            class="btn btn-warning">
                            @if (isset($slider->status) && $slider->status == 'Active')
                                <i data-feather="pause" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_inactive') }}
                            @elseif(isset($slider->status) && $slider->status == 'Inactive')
                                <i data-feather="play" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_active') }}
                            @endif
                        </a>
                    </div>
                    {{-- Delete --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.sliders-softDelete', isset($slider->id) ? $slider->id : -1) }}"
                            class="confirm btn btn-danger">
                            <i data-feather="trash" class="fill-white feather-sm"></i>{{ __('dashboard.shared.delete') }}
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
            <div class="col-12">
                {{-- Form Section --}}
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('super_admin.sliders-update', isset($slider->id) ? $slider->id : -1) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- Title EN --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="title_en"
                                            class="form-control border border-info @error('title_en') border-danger @enderror"
                                            id="tb-name" value="{{ isset($slider->title_en) ? $slider->title_en : null }}"
                                            placeholder="{{ __('dashboard.shared.title_en') }}">
                                        <label for="tb-name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.title_en') }}
                                            <strong class="text-danger">
                                                @error('title_en')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- Title AR --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="title_ar"
                                            class="form-control border border-info @error('title_ar') border-danger @enderror"
                                            id="tb-name" value="{{ isset($slider->title_ar) ? $slider->title_ar : null }}"
                                            placeholder="{{ __('dashboard.shared.title_ar') }}">
                                        <label for="tb-name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.title_ar') }}
                                            <strong class="text-danger">
                                                @error('title_ar')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <select name="status"
                                            class="form-control form-select border border-info @error('status') border-danger @enderror custom_select_style">
                                            <option>{{ __('dashboard.shared.choose_status') }}</option>
                                            <option value="1" @if ($slider->status == 'Active') selected @endif
                                                @if ($slider->status == null) selected @endif>{{ __('dashboard.shared.active') }}
                                            </option>
                                            <option value="2" @if ($slider->status == 'Inactive') selected @endif>
                                                {{ __('dashboard.shared.inactive') }} </option>
                                        </select>
                                    </div>
                                </div>

                                {{-- type --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <select name="type"
                                            class="form-control form-select border border-info @error('type') border-danger @enderror custom_select_style"
                                            onchange="onChangeType(this)">
                                            <option>{{ __('dashboard.sliders.select_type') }}</option>
                                            <option value="1" @if ($slider->type == 'Image') selected @endif
                                                @if ($slider->type == null) selected @endif>{{ __('dashboard.shared.image') }}</option>
                                            <option value="2" @if ($slider->type == 'Video') selected @endif>{{ __('dashboard.shared.video') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Image --}}
                                <div class="col-md-6" id="imageSection">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.image') }} :
                                            {{-- <label style="color: red">Recommended Dimensions are : 1300 × 868 px</label> --}}

                                            <strong class="text-danger">
                                                @error('image')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                        <input type="file" name="image"
                                            class="form-control border border-info @error('image') border-danger @enderror"
                                            id="image" value="{{ old('image') }}" placeholder="Image">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        @if (isset($slider->image) && $slider->getRawOriginal('image') && file_exists($slider->getRawOriginal('image')))
                                            <img src="{{ asset($slider->image) }}" width="100" height="100"
                                                style="border-radius: 10px; border:solid 1px black;">
                                        @else
                                            <img src="{{ asset('style_files\frontend\img\logo.jpg') }}" width="100"
                                                height="100" style="border-radius: 10px; border:solid 1px black;">
                                        @endif
                                    </div>
                                </div>


                                {{-- Video --}}
                                <div class="col-md-6" id="videoSection" style="display: none;">
                                    <div class="mb-3">
                                        <label for="video" class="form-label">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.video') }}
                                            :
                                            {{-- <label style="color: red">Recommended Dimensions are : 1300 × 868 px</label> --}}

                                            <strong class="text-danger">
                                                @error('video')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                        <input type="file" name="video"
                                            class="form-control border border-info @error('video') border-danger @enderror"
                                            id="video" value="{{ old('video') }}" placeholder="Video">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        @if (isset($slider->video) &&
                                                file_exists($slider->getRawOriginal('video')) &&
                                                strstr(mime_content_type($slider->getRawOriginal('video')), 'video/'))
                                            <video src="{{ asset($slider->video) }}" muted height="400" width="400"
                                                class="img-thumbnail image-preview" controls width="200px"></video>
                                        @else
                                            <img src="{{ asset('style_files/shared/images_default/default.jpg') }}"
                                                alt="Image" class="img-thumbnail image-preview">
                                        @endif
                                    </div>
                                </div>

                                <style>
                                    .fit-image {
                                        width: 100%;
                                        height: 100%;
                                        object-fit: contain;
                                    }
                                </style>


                                {{-- Description EN --}}
                                <div class="col-md-12">
                                    <h5> {{ __('dashboard.shared.description_en') }} :</h5>
                                    <textarea name="description_en"
                                        class="form-control border border-info @error('description_en') border-danger @enderror en"
                                        id="tb-description-en" placeholder="{{ __('dashboard.shared.description_en') }}" rows="8">{{ isset($slider->description_en) ? $slider->description_en : null }}</textarea>
                                    <label for="tb-description-en">
                                        <strong class="text-danger">
                                            @error('description_en')
                                                ( {{ $message }} )
                                            @enderror
                                        </strong>
                                    </label>
                                </div>

                                {{-- Description AR --}}
                                <div class="col-md-12">
                                    <h5> {{ __('dashboard.shared.description_ar') }} :</h5>
                                    <textarea name="description_ar" rows="8"
                                        class="form-control border border-info @error('description_ar') border-danger @enderror ar"
                                        id="tb-description-ar" placeholder="{{ __('dashboard.shared.description_ar') }}">{{ isset($slider->description_ar) ? $slider->description_ar : null }}</textarea>
                                    <label for="tb-description-ar">
                                        <strong class="text-danger">
                                            @error('description_ar')
                                                ( {{ $message }} )
                                            @enderror
                                        </strong>
                                    </label>
                                </div>

                                {{-- Button --}}
                                <div class="col-12">
                                    <div class="d-md-flex align-items-center mt-3">
                                        <div class="ms-auto mt-3 mt-md-0">
                                            <button type="submit"
                                                class="btn btn-success font-weight-medium rounded-pill px-4">
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="save" class="feather-sm fill-white me-2"></i>
                                                    {{ __('dashboard.shared.save_updates') }}
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    {{-- JavaScript to show/hide sections based on selection and remember the selection --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var typeSelect = document.querySelector('[name="type"]');
            var imageSection = document.getElementById('imageSection');
            var videoSection = document.getElementById('videoSection');
            var imageInput = document.getElementById('image');
            var videoInput = document.getElementById('video');

            // Initially hide the video section
            videoSection.style.display = 'none';

            // Retrieve the previously selected value from localStorage
            var selectedType = localStorage.getItem('selectedType');
            if (selectedType) {
                typeSelect.value = selectedType;
                if (selectedType === '1') {
                    imageSection.style.display = 'block';
                    videoSection.style.display = 'none'; // Ensure video section is hidden
                } else if (selectedType === '2') {
                    imageSection.style.display = 'none';
                    videoSection.style.display = 'block';
                }
            }

            // Event listener for type select change
            typeSelect.addEventListener('change', function() {
                if (this.value === '1') {
                    imageSection.style.display = 'block';
                    videoSection.style.display = 'none';
                    // Reset the video input
                    videoInput.value = '';
                } else if (this.value === '2') {
                    imageSection.style.display = 'none';
                    videoSection.style.display = 'block';
                    // Reset the image input
                    imageInput.value = '';
                }

                // Store the selected value in localStorage
                localStorage.setItem('selectedType', this.value);
            });
        });
    </script>
@endsection
