@extends('frontend.layouts.app')

@section('page', 'product-details')
@section('title'){{ $productName }} | Aurum Detection Co.@endsection
@section('product'){{ $slug }}@endsection
@section('main-id', 'product-details-app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/product-details.css') }}" />
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
    <script src="{{ asset('frontend/js/icons.js') }}"></script>
    <script src="{{ asset('frontend/js/products-data.js') }}"></script>
    <script src="{{ asset('frontend/js/product-details.js') }}"></script>
@endsection
