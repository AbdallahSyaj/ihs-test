@extends('frontend.layouts.app')

@section('page', 'cart')
@section('title', 'Equipment Inquiry | Aurum Detection Co.')
@section('shell-class', 'app-scroll')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/cart.css') }}" />
@endsection

@section('content')
    <div class="cart animate-fade-in">
        <section class="cart__hero">
            <div class="container-lux">
                <div class="section-label">Equipment Inquiry</div>
                <h1 class="cart__title">Your <span class="gold-text">Selection</span></h1>
                <p class="cart__lead">
                    Review the equipment you have added, adjust quantities, and send us your details. We will follow up with
                    availability, compatibility, and next steps — no online checkout.
                </p>
            </div>
        </section>

        <section class="cart__body">
            <div class="container-lux cart__grid">
                <div class="cart__items-panel">
                    <div class="cart__panel-head">
                        <h2 class="cart__panel-title">Selected equipment</h2>
                        <span class="cart__count" id="cart-item-count" aria-live="polite"></span>
                    </div>
                    <div id="cart-items" class="cart__list" aria-live="polite"></div>
                    <div id="cart-empty" class="cart-empty" hidden>
                        <p>Your inquiry list is empty. Browse the catalog and add equipment from any product page.</p>
                        <a href="{{ route('frontend.products') }}">Explore products</a>
                    </div>
                </div>

                <div class="cart__form-panel">
                    <div class="cart__panel-head">
                        <h2 class="cart__panel-title">Your details</h2>
                    </div>
                    <form id="cart-inquiry-form" class="cart-form" method="post"
                        action="{{ route('frontend.cart.store') }}" novalidate>
                        @csrf
                        <div class="cart-form__row">
                            <div class="cart-form__field--full">
                                <label for="inquiry-name">Full name</label>
                                <input id="inquiry-name" class="input-lux" type="text" name="name"
                                    value="{{ old('name') }}" placeholder="Your full name" autocomplete="name" />
                                <p class="cart-form__error" id="error-name"
                                    @unless ($errors->has('name')) hidden @endunless>
                                    {{ $errors->first('name') ?: 'Please enter your name' }}</p>
                            </div>
                            <div>
                                <label for="inquiry-email">Email</label>
                                <input id="inquiry-email" class="input-lux" type="email" name="email"
                                    value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" />
                                <p class="cart-form__error" id="error-email"
                                    @unless ($errors->has('email')) hidden @endunless>
                                    {{ $errors->first('email') ?: 'Please enter a valid email' }}</p>
                            </div>
                            <div>
                                <label for="inquiry-phone">Phone</label>
                                <input id="inquiry-phone" class="input-lux" type="tel" name="phone"
                                    value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" autocomplete="tel" />
                                <p class="cart-form__error" id="error-phone"
                                    @unless ($errors->has('phone')) hidden @endunless>
                                    {{ $errors->first('phone') ?: 'Please enter your phone number' }}</p>
                            </div>
                        </div>
                        <p class="cart-form__hint" id="cart-form-hint">
                            Add at least one product before submitting your inquiry.
                        </p>
                        <p class="cart-form__error" id="cart-form-error" hidden></p>
                        <div class="cart-form__submit">
                            <button type="submit" class="btn-gold" id="cart-submit" disabled>
                                <svg id="cart-submit-icon-default" class="icon icon-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="m22 2-7 20-4-9-9-4Z"></path>
                                    <path d="M22 2 11 13"></path>
                                </svg>
                                <svg id="cart-submit-icon-success" class="icon icon-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    aria-hidden="true" hidden>
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                                <span id="cart-submit-label-default">Submit inquiry</span>
                                <span id="cart-submit-label-success" hidden>Inquiry sent</span>
                            </button>
                            <span class="cart-form__thanks animate-fade-in" id="cart-thanks"
                                @unless (session('inquiry_success')) hidden @endunless>{{ session('inquiry_success', 'Thank you — your equipment inquiry has been received.') }}</span>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
    <script src="{{ asset('frontend/js/cart-page.js') }}"></script>
@endsection
