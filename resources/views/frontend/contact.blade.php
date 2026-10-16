@extends('frontend.layouts.app')

@section('page', 'contact')
@section('title', 'Contact Us | Aurum Detection Co.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/pages/contact.css') }}" />
@endsection

@section('content')
        <div class="contact animate-fade-in">
          <div class="container-lux contact__grid">
            <div class="contact__info">
              <div class="section-label">Contact Us</div>
              <h1 class="contact__title">
                Let's Find the Right<br />
                <span class="gold-text">Equipment.</span>
              </h1>
              <p class="contact__lead">
                Questions about a product, equipment compatibility, or which detector suits your terrain? Send us a message — we respond with practical, field-informed guidance.
              </p>
              <div class="divider-gold"></div>
              <div class="contact__cards">
                <div class="contact__card animate-fade-up" style="animation-delay: 0ms">
                  <div class="contact__icon">
                    <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                  </div>
                  <div>
                    <dt>Address</dt>
                    <dd>
                      @if (isset($contactUs) && filled($contactUs->address_en))
                        {!! nl2br(e($contactUs->address_en)) !!}
                      @else
                        [Store address placeholder]
                      @endif
                    </dd>
                  </div>
                </div>
                <div class="contact__card animate-fade-up" style="animation-delay: 60ms">
                  <div class="contact__icon">
                    <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                  </div>
                  <div>
                    <dt>Phone</dt>
                    <dd>
                      @if (isset($contactUs) && filled($contactUs->phone))
                        <a href="tel:{{ preg_replace('/\s+/', '', $contactUs->phone) }}">{{ $contactUs->phone }}</a>
                      @else
                        [Phone placeholder]
                      @endif
                    </dd>
                  </div>
                </div>
                <div class="contact__card animate-fade-up" style="animation-delay: 120ms">
                  <div class="contact__icon">
                    <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                  </div>
                  <div>
                    <dt>Email</dt>
                    <dd>
                      @if (isset($contactUs) && filled($contactUs->email))
                        <a href="mailto:{{ $contactUs->email }}">{{ $contactUs->email }}</a>
                      @else
                        [Email placeholder]
                      @endif
                    </dd>
                  </div>
                </div>
                <div class="contact__card animate-fade-up" style="animation-delay: 180ms">
                  <div class="contact__icon">
                    <svg class="icon icon-3_5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                  </div>
                  <div>
                    <dt>Hours</dt>
                    <dd>
                      @if (isset($contactUs) && filled($contactUs->hours))
                        {!! nl2br(e($contactUs->hours)) !!}
                      @else
                        [Operating hours placeholder]
                      @endif
                    </dd>
                  </div>
                </div>
              </div>
            </div>

            <div class="contact__form-wrap">
              <form id="contact-form" class="contact-form" method="post" action="{{ route('frontend.contact.store') }}" novalidate>
                @csrf
                <div class="contact-form__row">
                  <div>
                    <label for="contact-name">Name</label>
                    <input id="contact-name" class="input-lux" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" />
                    <p class="contact-form__error" id="error-name" @unless($errors->has('name')) hidden @endunless>{{ $errors->first('name') ?: 'Please enter your name' }}</p>
                  </div>
                  <div>
                    <label for="contact-email">Email</label>
                    <input id="contact-email" class="input-lux" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" />
                    <p class="contact-form__error" id="error-email" @unless($errors->has('email')) hidden @endunless>{{ $errors->first('email') ?: 'Please enter a valid email' }}</p>
                  </div>
                </div>
                <div>
                  <label for="contact-subject">Subject</label>
                  <input id="contact-subject" class="input-lux" type="text" name="subject" value="{{ old('subject') }}" placeholder="What can we help you with?" />
                  <p class="contact-form__error" id="error-subject" @unless($errors->has('subject')) hidden @endunless>{{ $errors->first('subject') ?: 'Please enter a subject' }}</p>
                </div>
                <div>
                  <label for="contact-message">Message</label>
                  <textarea id="contact-message" class="input-lux" name="message" rows="4" placeholder="Tell us about the equipment you're considering...">{{ old('message') }}</textarea>
                  <p class="contact-form__error" id="error-message" @unless($errors->has('message')) hidden @endunless>{{ $errors->first('message') ?: 'Please enter your message' }}</p>
                </div>
                <p class="contact-form__error" id="contact-form-error" hidden></p>
                <div class="contact-form__submit">
                  <button type="submit" class="btn-gold" id="contact-submit">
                    <svg class="icon icon-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="m22 2-7 20-4-9-9-4Z"></path>
                      <path d="M22 2 11 13"></path>
                    </svg>
                    Send Message
                  </button>
                  <span class="contact-form__thanks animate-fade-in" id="contact-thanks" @unless(session('contact_success')) hidden @endunless>{{ session('contact_success', "Thank you — we'll be in touch shortly.") }}</span>
                </div>
              </form>
            </div>
          </div>
        </div>
@endsection

@section('scripts')
    <script src="{{ asset('frontend/js/nav.js') }}"></script>
    <script src="{{ asset('frontend/js/icons.js') }}"></script>
    <script src="{{ asset('frontend/js/contact.js') }}"></script>
@endsection
