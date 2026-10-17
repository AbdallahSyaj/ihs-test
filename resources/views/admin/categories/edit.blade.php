@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.categories') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('super_admin.categories-index') }}">{{ __('dashboard.shared.modules.categories') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.update_info') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.categories-create') }}" class="btn btn-dark">
                            <i data-feather="plus" class="fill-white feather-sm"></i> {{ __('dashboard.shared.create_new') }}
                        </a>
                    </div>
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.categories-softDelete', isset($category->id) ? $category->id : -1) }}"
                            class="confirm btn btn-danger">
                            <i data-feather="trash" class="fill-white feather-sm"></i> {{ __('dashboard.shared.delete') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('super_admin.categories-update', isset($category->id) ? $category->id : -1) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" name="name"
                                        class="form-control border border-info @error('name') border-danger @enderror"
                                        id="category-name"
                                        value="{{ old('name', $category->name ?? '') }}"
                                        placeholder="{{ __('dashboard.shared.name') }}">
                                    <label for="category-name">
                                        <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.name') }}
                                        <strong class="text-danger">
                                            @error('name')
                                                ( {{ $message }} )
                                            @enderror
                                        </strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" name="slug"
                                        class="form-control border border-info @error('slug') border-danger @enderror"
                                        id="category-slug"
                                        value="{{ old('slug', $category->slug ?? '') }}"
                                        placeholder="{{ __('dashboard.categories.slug') }}"
                                        autocomplete="off">
                                    <label for="category-slug">
                                        <i data-feather="link" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.categories.slug') }}
                                        <strong class="text-danger">
                                            @error('slug')
                                                ( {{ $message }} )
                                            @enderror
                                        </strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i data-feather="save" class="feather-sm fill-white me-2"></i>{{ __('dashboard.shared.save_updates') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
