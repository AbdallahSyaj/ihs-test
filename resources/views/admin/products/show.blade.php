@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-8">
                <h3 class="page-title">{{ $product->name }}</h3>
                <p class="text-muted"><code>{{ $product->slug }}</code> · {{ $product->category?->name }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('super_admin.products-edit', $product->id) }}" class="btn btn-secondary">{{ __('dashboard.shared.edit') }}</a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <p><strong>{{ __('dashboard.shared.status') }}:</strong> {{ $product->status }}</p>
                <p><strong>{{ __('dashboard.products.base_price') }}:</strong> {{ number_format((float) $product->base_price, 2) }}</p>
                @if ($product->model)<p><strong>{{ __('dashboard.products.model') }}:</strong> {{ $product->model }}</p>@endif
                @if ($product->badge)<p><strong>{{ __('dashboard.products.badge') }}:</strong> {{ $product->badge }}</p>@endif
                <p>{{ $product->description }}</p>
                <hr>
                <h5>{{ __('dashboard.products.specifications') }}</h5>
                <ul>
                    @foreach ($product->specs as $spec)
                        <li>{{ $spec->label }}: {{ $spec->value }}</li>
                    @endforeach
                </ul>
                <h5>{{ __('dashboard.products.accessories') }}</h5>
                <ul>
                    @foreach ($product->accessories as $accessory)
                        <li>{{ $accessory->name }} ({{ $accessory->qty }})</li>
                    @endforeach
                </ul>
                <h5>{{ __('dashboard.products.options') }}</h5>
                @foreach ($product->options as $option)
                    <p class="fw-bold">{{ $option->name }}</p>
                    <ul>
                        @foreach ($option->values as $value)
                            <li>{{ $value->label }} (+{{ number_format((float) $value->additional_price, 2) }})</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
@endsection
