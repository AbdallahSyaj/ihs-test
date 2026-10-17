@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-8">
                <h3 class="page-title">{{ __('dashboard.shared.modules.products') }} — {{ __('dashboard.shared.view_archive') }}</h3>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('super_admin.products-index') }}" class="btn btn-secondary">{{ __('dashboard.shared.modules.products') }}</a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.shared.name') }}</th>
                            <th>{{ __('dashboard.products.slug') }}</th>
                            <th>{{ __('dashboard.shared.control') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><code>{{ $product->slug }}</code></td>
                                <td>
                                    <a href="{{ route('super_admin.products-softDeleteRestore', $product->id) }}" class="btn btn-success btn-sm">{{ __('dashboard.shared.restore_this_record') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
