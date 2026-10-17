@extends('frontend.layouts.app')

@section('page', 'product-details')
@section('title'){{ $productName }} | Aurum Detection Co.@endsection
@section('product'){{ $slug }}@endsection
@section('main-id', 'product-details-app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/product-details.css') }}" />
@endsection

@section('content')
@php
    $gallery = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
    $variants = is_array($product['variants'] ?? null) ? $product['variants'] : [];
    $specs = is_array($product['specs'] ?? null) ? $product['specs'] : [];
    $accessories = is_array($product['accessories'] ?? null) ? $product['accessories'] : [];
    $mainImage = $gallery[0] ?? ($product['image'] ?? '');
@endphp
        <div class="pd animate-fade-in" data-product-name="{{ $product['name'] }}">
          <div class="container-lux pd__inner">
            <div class="pd__crumb">
              <a href="{{ route('frontend.products') }}">
                <svg class="icon icon-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="m15 18-6-6 6-6"></path>
                </svg>
                Products
              </a>
              <span class="pd__crumb-sep">/</span>
              <span class="pd__crumb-name">{{ $product['name'] }}</span>
            </div>
            <div class="pd__layout">
              <div class="pd__gallery">
                <div class="pd__main-image" id="pd-main">
                  @if ($mainImage !== '')
                    <img id="pd-main-img" class="animate-scale-in" src="{{ $mainImage }}" alt="{{ $product['name'] }} view 1" />
                  @endif
                  @if (! empty($product['badge']))
                    <span class="badge-gold" id="pd-main-badge">{{ $product['badge'] }}</span>
                  @endif
                </div>
                <div class="pd__thumbs" id="pd-thumbs">
                  @foreach ($gallery as $index => $image)
                    <button type="button" class="pd__thumb{{ $index === 0 ? ' is-active' : '' }}" data-index="{{ $index }}">
                      <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}" loading="lazy" />
                      <span class="pd__thumb-bar" @if ($index !== 0) hidden @endif></span>
                    </button>
                  @endforeach
                </div>
              </div>
              <div class="pd__info">
                <span class="pd__meta" id="pd-meta">{{ $product['categoryLabel'] }} — Model {{ $product['model'] }}</span>
                <h1 class="pd__title" id="pd-title">{{ $product['name'] }}</h1>
                <p class="pd__desc" id="pd-desc">{{ $product['description'] }}</p>
                <div class="pd__price-row">
                  <span class="pd__price" id="pd-price">{{ $product['price'] }}</span>
                  <span class="pd__stock"><span class="pd__stock-dot"></span>In stock — ready to ship</span>
                </div>
                <div class="divider-gold"></div>
                @if (count($variants) > 0)
                  <div class="pd__variants" id="pd-variants">
                    @foreach ($variants as $variant)
                      @if (is_array($variant) && isset($variant['name']) && is_array($variant['options'] ?? null))
                        <div class="pd__variant" data-variant="{{ $variant['name'] }}">
                          <label class="pd__variant-label">
                            <span>{{ $variant['name'] }}</span>
                            <span class="pd__variant-error" data-variant-error="{{ $variant['name'] }}" hidden>Please select a {{ strtolower($variant['name']) }}</span>
                          </label>
                          <div class="pd__select">
                            <button type="button" class="pd__select-btn" data-toggle="{{ $variant['name'] }}">
                              <span class="pd__select-value pd__select-placeholder">Select {{ $variant['name'] }}</span>
                              <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"></path>
                              </svg>
                            </button>
                            <div class="pd__select-menu animate-fade-in" data-variant-menu="{{ $variant['name'] }}" hidden>
                              @foreach ($variant['options'] as $option)
                                <button type="button" class="pd__select-option" data-variant="{{ $variant['name'] }}" data-option="{{ $option }}">
                                  <span>{{ $option }}</span>
                                  <svg class="pd__option-check icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
                                    <path d="M20 6 9 17l-5-5"></path>
                                  </svg>
                                </button>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endif
                <div class="pd__actions">
                  <button type="button" class="btn-gold" id="pd-cart">
                    <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                      <path d="M3 6h18"></path>
                      <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    Add to Cart
                  </button>
                  <a href="{{ route('frontend.contact') }}" class="btn-outline">Request Details</a>
                </div>
                {{-- <div class="pd__tabs">
                  <div class="pd__tablist" id="pd-tabs">
                    <button type="button" class="pd__tab is-active" data-tab="description">
                      <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                        <path d="M10 9H8"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                      </svg>
                      Description
                    </button>
                    <button type="button" class="pd__tab" data-tab="specs">
                      <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 16v-4"></path>
                        <path d="M12 8h.01"></path>
                      </svg>
                      Specifications
                    </button>
                    <button type="button" class="pd__tab" data-tab="accessories">
                      <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                      </svg>
                      Accessories
                    </button>
                    <button type="button" class="pd__tab" data-tab="compatibility">
                      <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 17H7A5 5 0 0 1 7 7h2"></path>
                        <path d="M15 7h2a5 5 0 1 1 0 10h-2"></path>
                        <line x1="8" x2="16" y1="12" y2="12"></line>
                      </svg>
                      Compatibility
                    </button>
                  </div>
                  <div class="pd__tab-panel" id="pd-panel">
                    <div data-tab-panel="description">
                      <p class="pd__tab-text animate-fade-in">{{ $product['longDescription'] }}</p>
                    </div>
                    <div data-tab-panel="specs" hidden>
                      <div class="animate-fade-in">
                        @foreach ($specs as $spec)
                          @if (is_array($spec) && isset($spec['label'], $spec['value']))
                            <div class="pd__spec">
                              <span class="pd__spec-label">{{ $spec['label'] }}</span>
                              <span class="pd__spec-value">{{ $spec['value'] }}</span>
                            </div>
                          @endif
                        @endforeach
                      </div>
                    </div>
                    <div data-tab-panel="accessories" hidden>
                      <ul class="animate-fade-in">
                        @foreach ($accessories as $accessory)
                          @if (is_array($accessory) && isset($accessory['name'], $accessory['qty']))
                            <li class="pd__acc">
                              <div class="pd__acc-name">
                                <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                  <path d="m7.5 4.27 9 5.15"></path>
                                  <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                  <path d="m3.3 7 8.7 5 8.7-5"></path>
                                  <path d="M12 22V12"></path>
                                </svg>
                                <span>{{ $accessory['name'] }}</span>
                              </div>
                              <span class="pd__acc-qty">×{{ $accessory['qty'] }}</span>
                            </li>
                          @endif
                        @endforeach
                      </ul>
                    </div>
                    <div data-tab-panel="compatibility" hidden>
                      <p class="pd__tab-text animate-fade-in">{{ $product['compatibility'] }}</p>
                    </div>
                  </div>
                </div> --}}
              </div>
            </div>
          </div>
        </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
    <script src="{{ asset('frontend/js/product-details.js') }}"></script>
@endsection
