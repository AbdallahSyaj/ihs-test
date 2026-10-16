@extends('frontend.layouts.app')

@section('page', 'products')
@section('title', 'Products | Aurum Detection Co.')
@section('shell-class', 'app-scroll')
@section('shell-id', 'products-app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/products.css') }}" />
@endsection

@section('content')
        <div class="animate-fade-in">
          <section class="products-hero">
            <div class="container-lux">
              <div class="products-hero__row">
                <div class="products-hero__copy">
                  <div class="section-label">Our Catalog</div>
                  <h1 class="products-hero__title">Explore Our Equipment</h1>
                  <p class="products-hero__lead">
                    Professional gold detectors, metal detectors, prospecting gear, and accessories — each selected for field-ready performance and build quality.
                  </p>
                </div>
                <div class="products-search">
                  <div class="products-search__field">
                    <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <circle cx="11" cy="11" r="8"></circle>
                      <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input id="products-search" type="text" placeholder="Search equipment..." aria-label="Search equipment" />
                  </div>
                </div>
              </div>
            </div>
          </section>

          <section class="products-filters">
            <div class="container-lux products-filters__inner">
              <div class="products-filters__row">
                <div class="products-filters__label">
                  <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="21" x2="14" y1="4" y2="4"></line>
                    <line x1="10" x2="3" y1="4" y2="4"></line>
                    <line x1="21" x2="12" y1="12" y2="12"></line>
                    <line x1="8" x2="3" y1="12" y2="12"></line>
                    <line x1="21" x2="16" y1="20" y2="20"></line>
                    <line x1="12" x2="3" y1="20" y2="20"></line>
                    <line x1="14" x2="14" y1="2" y2="6"></line>
                    <line x1="8" x2="8" y1="10" y2="14"></line>
                    <line x1="16" x2="16" y1="18" y2="22"></line>
                  </svg>
                  <span>Filter</span>
                </div>
                <div class="products-filters__rule"></div>
                <div id="filter-chips" class="products-filters__chips"></div>
              </div>
            </div>
          </section>

          <section class="products-grid-wrap">
            <div class="container-lux">
              <div class="products-count">
                <span id="products-count">0 items</span>
              </div>
              <div id="products-grid" class="products-grid"></div>
              <div id="products-empty" class="products-empty" hidden>
                <p>No equipment matches your search.</p>
                <button type="button" id="clear-filters">Clear filters</button>
              </div>
            </div>
          </section>
        </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
    <script src="{{ asset('frontend/js/icons.js') }}"></script>
    <script src="{{ asset('frontend/js/products-data.js') }}"></script>
    <script src="{{ asset('frontend/js/products-page.js') }}"></script>
@endsection
