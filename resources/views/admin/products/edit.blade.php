@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-8 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.products') }} — {{ __('dashboard.shared.edit') }}</h3>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('super_admin.products-update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.products._form', ['categories' => $categories, 'product' => $product])
                    <button type="submit" class="btn btn-success mt-3">
                        <i data-feather="save" class="feather-sm fill-white me-2"></i>{{ __('dashboard.shared.save_updates') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    @include('admin.products._form_scripts')
@endsection
