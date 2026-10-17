@extends('frontend.layouts.app')

@section('page', 'about')
@section('title', 'About Us | Aurum Detection Co.')
@section('shell-class', 'app-scroll')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/about.css') }}" />
@endsection

@section('content')
    <div class="about animate-fade-in">
        <div class="container-lux about__grid">
            <div class="about__text">
                <div class="section-label">About Aurum Detection Co.</div>
                <h1 class="about__title">
                    @if (isset($aboutUs) && filled($aboutUs->title_en))
                        {{ $aboutUs->title_en }}
                    @else
                        Built for the Search.
                    @endif
                </h1>
                <p class="about__p" @if (isset($aboutUs) && filled($aboutUs->description_en)) style="white-space: pre-line" @endif>
                    @if (isset($aboutUs) && filled($aboutUs->description_en))
                        {{ $aboutUs->description_en }}
                    @else
                        Aurum Detection Co. exists for a specific kind of customer — the prospector who treats detection as
                        a craft, not a hobby. We stock professional gold detectors, metal detectors, and prospecting
                        equipment, and we know how each piece performs in the field.
                    @endif
                </p>
                <div class="divider-gold"></div>
                <a href="{{ route('frontend.products') }}" class="about__link">
                    Explore Equipment
                    <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <div class="about__media">
                @if (isset($aboutUs) && filled($aboutUs->image) && file_exists($aboutUs->image))
                    <img src="{{ asset($aboutUs->image) }}" alt="Prospectors using detection equipment at dawn"
                        loading="lazy" />
                @else
                    <img src="https://images.pexels.com/photos/39826625/pexels-photo-39826625.jpeg?auto=compress&cs=tinysrgb&w=1200&h=1400&fit=crop"
                        alt="Prospectors using detection equipment at dawn" loading="lazy" />
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
@endsection
