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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.update_info') }}</li>
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

                    {{-- Show --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.users-show', isset($user->id) ? $user->id : -1) }}"
                            class="btn btn-primary">
                            <i data-feather="eye" class="fill-white feather-sm"></i>{{ __('dashboard.shared.view') }}
                        </a>
                    </div>

                    {{-- Active / Inactive --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.users-activeInactiveSingle', isset($user->id) ? $user->id : -1) }}"
                            class="btn btn-warning">
                            @if (isset($user->status) && $user->status == 'Active')
                                <i data-feather="pause" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_inactive') }}
                            @elseif(isset($user->status) && $user->status == 'Inactive')
                                <i data-feather="play" class="fill-white feather-sm"></i>{{ __('dashboard.shared.set_active') }}
                            @endif
                        </a>
                    </div>

                    {{-- Delete --}}
                    <div class="dropdown me-2">
                        <a href="{{ route('super_admin.users-softDelete', isset($user->id) ? $user->id : -1) }}"
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
                        <form
                            action="{{ route('super_admin.users-update', isset($user->id) ? $user->id : -1) }}"
                            method="POST" enctype="multipart/form-data" id="editForm">
                            @csrf
                            <div class="row">
                                {{-- First Name --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="first_name"
                                            class="form-control border border-info @error('first_name') border-danger @enderror"
                                            id="tb-name"
                                            value="{{ old('first_name', isset($user->first_name) ? $user->first_name : null) }}"
                                            placeholder="{{ __('dashboard.shared.first_name') }}">
                                        <label for="tb-name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.first_name') }}
                                            <strong class="text-danger">
                                                @error('first_name')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="last_name"
                                            class="form-control border border-info @error('last_name') border-danger @enderror"
                                            id="tb-name"
                                            value="{{ old('last_name', isset($user->last_name) ? $user->last_name : null) }}"
                                            placeholder="{{ __('dashboard.shared.last_name') }}">
                                        <label for="tb-Name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.last_name') }}
                                            <strong class="text-danger">
                                                @error('last_name')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="email"
                                            class="form-control border border-info @error('email') border-danger @enderror"
                                            id="tb-name"
                                            value="{{ old('email', isset($user->email) ? $user->email : null) }}"
                                            placeholder="{{ __('dashboard.shared.email') }}">
                                        <label for="tb-Name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.email') }}
                                            <strong class="text-danger">
                                                @error('email')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="numbers" name="phone" min="0"
                                            class="form-control border border-info @error('phone') border-danger @enderror"
                                            id="tb-name"
                                            value="{{ old('phone', isset($user->phone) ? $user->phone : null) }}"
                                            placeholder="{{ __('dashboard.shared.phone') }}">
                                        <label for="tb-Name">
                                            <i data-feather="type" class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.phone') }}
                                            <strong class="text-danger">
                                                @error('phone')
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
                                            <option value="1" @if ($user->status == 'Active') selected @endif
                                                @if ($user->status == null) selected @endif>{{ __('dashboard.shared.active') }}
                                            </option>
                                            <option value="2" @if ($user->status == 'Inactive') selected @endif>
                                                {{ __('dashboard.shared.inactive') }} </option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password"
                                            class="form-control border border-info @error('password') border-danger @enderror"
                                            id="tb-name" value="{{ old('password') }}" placeholder="{{ __('dashboard.shared.password') }}">
                                        <label for="tb-name">
                                            <i data-feather="type"
                                                class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.password') }}
                                            <strong class="text-danger">
                                                @error('password')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
                                </div>

                                {{-- password_confirmation --}}
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password_confirmation"
                                            class="form-control border border-info @error('password_confirmation') border-danger @enderror"
                                            id="tb-name" value="{{ old('password_confirmation') }}"
                                            placeholder="{{ __('dashboard.shared.confirm_password') }}">
                                        <label for="tb-name">
                                            <i data-feather="type"
                                                class="feather-sm text-info fill-white me-2"></i>{{ __('dashboard.shared.confirm_password') }}
                                            <strong class="text-danger">
                                                @error('password_confirmation')
                                                    ( {{ $message }} )
                                                @enderror
                                            </strong>
                                        </label>
                                    </div>
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

