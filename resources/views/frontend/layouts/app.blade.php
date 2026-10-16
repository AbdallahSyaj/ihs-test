<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Aurum Detection Co. — Gold Prospecting Equipment')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Jost:wght@200;300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('frontend/css/tokens.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/reset.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/components.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/layout.css') }}" />
    @yield('styles')
  </head>
  <body data-page="@yield('page')"@hasSection('product') data-product="@yield('product')"@endif>
    <div class="@yield('shell-class', 'app-locked')"@hasSection('shell-id') id="@yield('shell-id')"@endif>
      @include('frontend.partials.header')
      <main @hasSection('main-id') id="@yield('main-id')" @endif>
        @yield('content')
      </main>
      @include('frontend.partials.footer')
    </div>
    <script>
      window.AurumPaths = {
        catalog: @json(asset('frontend/data/products.json')),
        products: @json(route('frontend.products')),
        contact: @json(route('frontend.contact')),
        productBase: @json(url('/products'))
      };
    </script>
    @yield('scripts')
    <script>
      (window.AurumOnReady || []).forEach(function (fn) {
        try {
          fn();
        } catch (err) {
          console.error(err);
        }
      });
    </script>
  </body>
</html>
