@extends('frontend.layouts.app')

@section('page', 'home')
@section('title', 'Aurum Detection Co. — Gold Prospecting Equipment')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/home.css') }}" />
@endsection

@section('content')
        <div class="home animate-fade-in">
          <div class="home__bg">
            <img src="https://images.pexels.com/photos/12683080/pexels-photo-12683080.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop" alt="Professional gold detecting in the field" />
            <div class="home__veil-y"></div>
            <div class="home__veil-x"></div>
          </div>
          <div class="container-lux home__content">
            <div class="home__copy">
              <div class="section-label animate-fade-down">Professional Gold Detection Equipment</div>
              <h1 class="home__title animate-fade-up" style="animation-delay: 100ms">
                Precision Beneath<br />
                <span class="gold-text">the Surface.</span>
              </h1>
              <p class="home__lead animate-fade-up" style="animation-delay: 200ms">
                Professional-grade gold detectors, metal detectors, and prospecting equipment — engineered for those who read the earth with intent.
              </p>
              <div class="home__actions animate-fade-up" style="animation-delay: 300ms">
                <a href="{{ route('frontend.products') }}" class="btn-gold">
                  Explore Equipment
                  <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                  </svg>
                </a>
                <a href="{{ route('frontend.about') }}" class="btn-outline">Discover Our Brand</a>
              </div>
            </div>
          </div>
        </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
@endsection
