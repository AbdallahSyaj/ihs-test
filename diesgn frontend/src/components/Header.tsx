import { Search, ShoppingBag, Menu, X, Crosshair } from 'lucide-react';
import { useRouter, Route } from '@/router';

const navItems: { label: string; route: Route }[] = [
  { label: 'Home', route: 'home' },
  { label: 'Products', route: 'products' },
  { label: 'About Us', route: 'about' },
  { label: 'Contact Us', route: 'contact' },
];

export default function Header() {
  const { route, navigate } = useRouter();

  return (
    <header className="relative z-50 shrink-0 border-b border-silver-muted/10 bg-ink-deep/80 backdrop-blur-md">
      <div className="container-lux flex h-[72px] items-center justify-between">
        {/* Logo */}
        <button
          onClick={() => navigate('home')}
          className="group flex items-center gap-2.5"
          aria-label="Aurum home"
        >
          <Crosshair className="h-6 w-6 text-gold transition-transform duration-500 group-hover:rotate-90" strokeWidth={1.25} />
          <div className="flex flex-col leading-none">
            <span className="font-serif text-lg font-medium tracking-wide text-cream">AURUM</span>
            <span className="text-[9px] font-light uppercase tracking-mega text-silver-muted">Detection Co.</span>
          </div>
        </button>

        {/* Desktop Nav */}
        <nav className="hidden items-center gap-10 lg:flex">
          {navItems.map((item) => (
            <button
              key={item.route}
              onClick={() => navigate(item.route)}
              className={`nav-link ${route === item.route || (item.route === 'products' && route === 'product-details') ? 'nav-link-active' : ''}`}
            >
              {item.label}
            </button>
          ))}
        </nav>

        {/* Actions */}
        <div className="flex items-center gap-4">
          <button
            className="text-silver-muted transition-colors hover:text-gold"
            aria-label="Search"
          >
            <Search className="h-[18px] w-[18px]" strokeWidth={1.5} />
          </button>
          <button
            onClick={() => navigate('products')}
            className="text-silver-muted transition-colors hover:text-gold"
            aria-label="Cart"
          >
            <ShoppingBag className="h-[18px] w-[18px]" strokeWidth={1.5} />
          </button>
          <button
            className="text-silver-muted transition-colors hover:text-gold lg:hidden"
            aria-label="Menu"
          >
            <Menu className="h-5 w-5" strokeWidth={1.5} />
          </button>
        </div>
      </div>
    </header>
  );
}
