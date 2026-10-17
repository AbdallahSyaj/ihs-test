<header class="site-header">
  <div class="container-lux site-header__inner">
    <a href="{{ route('frontend.home') }}" class="site-logo" aria-label="Aurum home">
      <svg class="icon icon-6 site-logo__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="22" x2="18" y1="12" y2="12"></line>
        <line x1="6" x2="2" y1="12" y2="12"></line>
        <line x1="12" x2="12" y1="6" y2="2"></line>
        <line x1="12" x2="12" y1="22" y2="18"></line>
      </svg>
      <div class="site-logo__name">
        <span class="site-logo__brand">AURUM</span>
        <span class="site-logo__sub">Detection Co.</span>
      </div>
    </a>

    <nav class="site-nav" id="site-nav" aria-label="Primary">
      <a href="{{ route('frontend.home') }}" class="nav-link" data-nav="home">Home</a>
      <a href="{{ route('frontend.products') }}" class="nav-link" data-nav="products">Products</a>
      <a href="{{ route('frontend.about') }}" class="nav-link" data-nav="about">About Us</a>
      <a href="{{ route('frontend.contact') }}" class="nav-link" data-nav="contact">Contact Us</a>
    </nav>

    <div class="site-actions">
      <button class="site-actions__btn" type="button" aria-label="Search">
        <svg class="icon icon-18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.3-4.3"></path>
        </svg>
      </button>
      <a href="{{ route('frontend.cart') }}" class="site-actions__btn site-actions__cart" aria-label="Equipment inquiry cart">
        <svg class="icon icon-18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
          <path d="M3 6h18"></path>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span class="site-actions__badge" id="site-cart-badge" hidden>0</span>
      </a>
      <button class="site-actions__btn site-actions__menu" type="button" id="site-menu-toggle"
        aria-label="Menu" aria-expanded="false" aria-controls="site-nav">
        <svg class="icon icon-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="4" x2="20" y1="12" y2="12"></line>
          <line x1="4" x2="20" y1="6" y2="6"></line>
          <line x1="4" x2="20" y1="18" y2="18"></line>
        </svg>
      </button>
    </div>
  </div>
  <button type="button" class="site-nav-backdrop" id="site-nav-backdrop" hidden
    aria-label="Close menu" tabindex="-1"></button>
</header>
