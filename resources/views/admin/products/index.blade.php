@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.products') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.modules.products') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
                <div class="d-flex">
                    <a href="{{ route('super_admin.products-create') }}" class="btn btn-dark me-2">
                        <i data-feather="plus" class="fill-white feather-sm"></i> {{ __('dashboard.shared.create_new') }}
                    </a>
                    <a href="{{ route('super_admin.products-showSoftDelete') }}" class="btn btn-danger">
                        <i data-feather="archive" class="fill-white feather-sm"></i> {{ __('dashboard.shared.view_archive') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" action="{{ route('super_admin.products-index') }}" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="{{ __('dashboard.products.search_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">{{ __('dashboard.products.filter_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('dashboard.products.filter_status') }}</option>
                            <option value="active" @selected(request('status') === 'active')>{{ __('dashboard.shared.active') }}</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('dashboard.shared.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('dashboard.datatable.search') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.shared.name') }}</th>
                                        <th>{{ __('dashboard.products.slug') }}</th>
                                        <th>{{ __('dashboard.products.category') }}</th>
                                        <th>{{ __('dashboard.products.base_price') }}</th>
                                        <th>{{ __('dashboard.shared.status') }}</th>
                                        <th>{{ __('dashboard.shared.control') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('super_admin.products-edit', $product->id) }}">{{ $product->name }}</a>
                                            </td>
                                            <td><code>{{ $product->slug }}</code></td>
                                            <td>{{ $product->category?->name ?? '—' }}</td>
                                            <td>{{ number_format((float) $product->base_price, 2) }}</td>
                                            <td>
                                                @if ($product->status === 'active')
                                                    <span class="badge bg-success">{{ __('dashboard.shared.active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('dashboard.shared.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="button-group">
                                                    <a href="{{ route('super_admin.products-show', $product->id) }}" class="btn btn-info btn-sm" title="{{ __('dashboard.shared.view') }}"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('super_admin.products-edit', $product->id) }}" class="btn btn-secondary btn-sm" title="{{ __('dashboard.shared.edit') }}"><i class="fas fa-edit"></i></a>
                                                    <a href="{{ route('super_admin.products-activeInactiveSingle', $product->id) }}" class="btn btn-warning btn-sm" title="{{ __('dashboard.shared.status') }}"><i class="fas fa-toggle-on"></i></a>
                                                    <a href="{{ route('super_admin.products-softDelete', $product->id) }}" class="confirm btn btn-danger btn-sm" title="{{ __('dashboard.shared.delete') }}"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">—</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
