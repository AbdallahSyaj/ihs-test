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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.modules.categories') }}</li>
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
                        <a href="{{ route('super_admin.categories-showSoftDelete') }}" class="btn btn-danger">
                            <i data-feather="archive" class="fill-white feather-sm"></i> {{ __('dashboard.shared.view_archive') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                        <th>{{ __('dashboard.shared.date_time') }}</th>
                                        <th>{{ __('dashboard.shared.control') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($categories) && $categories->count() > 0)
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('super_admin.categories-edit', $category->id) }}">
                                                        {{ $category->name ?? '----' }}
                                                    </a>
                                                </td>
                                                <td>{!! isset($category->created_at)
                                                    ? '<strong>' . __('dashboard.shared.date_label') . ' : </strong>' .
                                                        date('Y -m-d', strtotime($category->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.time_label') . ' : </strong>' .
                                                        date('h:i A', strtotime($category->created_at)) .
                                                        '<br><strong>' . __('dashboard.shared.since_label') . ' : </strong> ' .
                                                        $category->created_at->diffForHumans()
                                                    : "<span style='color:blue;'>----------</span>" !!}</td>
                                                <td>
                                                    <div class="button-group">
                                                        <a href="{{ route('super_admin.categories-edit', $category->id) }}"
                                                            class="btn waves-effect waves-light btn-secondary btn-sm"
                                                            title="{{ __('dashboard.shared.edit') }}"><i class="fas fa-edit"></i></a>
                                                        <a href="{{ route('super_admin.categories-softDelete', $category->id) }}"
                                                            class="confirm btn waves-effect waves-light btn-danger btn-sm"
                                                            title="{{ __('dashboard.shared.delete') }}"><i class="fas fa-trash-alt"></i></a>
                                                    </div>
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
