import { useState, useMemo } from 'react';
import { ChevronDown, Check, ChevronLeft, ShoppingBag, Info, FileText, Package, Link2 } from 'lucide-react';
import { useRouter } from '@/router';
import { products } from '@/data/products';

type TabId = 'description' | 'specs' | 'accessories' | 'compatibility';

export default function ProductDetailsPage() {
  const { productId, navigate } = useRouter();
  const product = useMemo(() => products.find((p) => p.id === productId) ?? products[0], [productId]);

  const [activeImage, setActiveImage] = useState(0);
  const [activeTab, setActiveTab] = useState<TabId>('description');
  const [selectedVariants, setSelectedVariants] = useState<Record<string, string>>({});
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);
  const [showValidation, setShowValidation] = useState(false);

  const tabs: { id: TabId; label: string; icon: typeof FileText }[] = [
    { id: 'description', label: 'Description', icon: FileText },
    { id: 'specs', label: 'Specifications', icon: Info },
    { id: 'accessories', label: 'Accessories', icon: Package },
    { id: 'compatibility', label: 'Compatibility', icon: Link2 },
  ];

  const handleSelect = (variantName: string, option: string) => {
    setSelectedVariants((prev) => ({ ...prev, [variantName]: option }));
    setOpenDropdown(null);
  };

  const handleAddToCart = () => {
    const unselected = product.variants.filter((v) => !selectedVariants[v.name]);
    if (unselected.length > 0) {
      setShowValidation(true);
    } else {
      setShowValidation(false);
    }
  };

  return (
    <div className="h-full w-full overflow-hidden animate-fade-in">
      <div className="container-lux flex h-full flex-col py-5">
        {/* Breadcrumb */}
        <div className="mb-4 flex shrink-0 items-center gap-2 text-xs font-light text-silver-muted">
          <button onClick={() => navigate('products')} className="hover:text-gold transition-colors flex items-center gap-1">
            <ChevronLeft className="h-3 w-3" />
            Products
          </button>
          <span className="text-silver-muted/40">/</span>
          <span className="text-gold">{product.name}</span>
        </div>

        {/* Main two-column layout */}
        <div className="grid min-h-0 flex-1 grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">
          {/* Gallery */}
          <div className="flex min-h-0 flex-col gap-3">
            <div className="relative min-h-0 flex-1 overflow-hidden bg-ink-charcoal border border-silver-muted/10">
              <img
                src={product.gallery[activeImage]}
                alt={`${product.name} view ${activeImage + 1}`}
                className="h-full w-full object-cover animate-scale-in"
                key={activeImage}
              />
              <div className="absolute inset-0 bg-gradient-to-t from-ink-deep/25 to-transparent" />
              {product.badge && (
                <span className="absolute top-4 left-4 bg-gold/90 px-3 py-1 text-[10px] font-medium uppercase tracking-ultra-wide text-ink-deep">
                  {product.badge}
                </span>
              )}
            </div>

            {/* Thumbnails */}
            <div className="grid shrink-0 grid-cols-4 gap-2.5">
              {product.gallery.map((img, i) => (
                <button
                  key={i}
                  onClick={() => setActiveImage(i)}
                  className={`relative aspect-square overflow-hidden border transition-all duration-300 ${
                    activeImage === i
                      ? 'border-gold ring-1 ring-gold/30'
                      : 'border-silver-muted/15 hover:border-silver-muted/40'
                  }`}
                >
                  <img src={img} alt={`Thumbnail ${i + 1}`} className="h-full w-full object-cover" loading="lazy" />
                  {activeImage === i && (
                    <div className="absolute bottom-0 left-0 h-0.5 w-full bg-gold" />
                  )}
                </button>
              ))}
            </div>
          </div>

          {/* Product Info */}
          <div className="flex min-h-0 flex-col overflow-y-auto pr-1">
            <span className="text-[10px] font-medium uppercase tracking-ultra-wide text-gold mb-2">
              {product.categoryLabel} — Model {product.model}
            </span>
            <h1 className="font-serif text-2xl font-light text-cream md:text-3xl lg:text-4xl leading-tight mb-3">
              {product.name}
            </h1>
            <p className="text-sm font-light leading-relaxed text-ash mb-4">
              {product.description}
            </p>

            <div className="flex items-center gap-4 mb-4">
              <span className="font-serif text-xl font-medium text-cream">{product.price}</span>
              <span className="flex items-center gap-2 text-xs font-light text-silver-muted">
                <span className="h-1.5 w-1.5 rounded-full bg-green-500/70" />
                In stock — ready to ship
              </span>
            </div>

            <div className="divider-gold mb-4" />

            {/* Variants */}
            {product.variants.length > 0 && (
              <div className="mb-4 space-y-3">
                {product.variants.map((variant) => {
                  const isOpen = openDropdown === variant.name;
                  const selected = selectedVariants[variant.name];
                  const showError = showValidation && !selected;

                  return (
                    <div key={variant.name}>
                      <label className="mb-1.5 block text-[10px] font-medium uppercase tracking-ultra-wide text-silver-muted flex items-center justify-between">
                        <span>{variant.name}</span>
                        {showError && (
                          <span className="text-[10px] text-red-400/80 normal-case tracking-normal">
                            Please select a {variant.name.toLowerCase()}
                          </span>
                        )}
                      </label>
                      <div className="relative">
                        <button
                          onClick={() => setOpenDropdown(isOpen ? null : variant.name)}
                          className={`flex w-full items-center justify-between bg-ink-charcoal/60 border px-4 py-2.5 text-sm font-light transition-all duration-300 ${
                            isOpen
                              ? 'border-gold ring-1 ring-gold/20'
                              : showError
                              ? 'border-red-400/40'
                              : selected
                              ? 'border-silver-muted/30 text-cream'
                              : 'border-silver-muted/20 text-ash'
                          }`}
                        >
                          <span className={selected ? 'text-cream' : 'text-ash/60'}>
                            {selected || `Select ${variant.name}`}
                          </span>
                          <ChevronDown
                            className={`h-4 w-4 text-silver-muted transition-transform duration-300 ${isOpen ? 'rotate-180' : ''}`}
                            strokeWidth={1.5}
                          />
                        </button>

                        {isOpen && (
                          <div className="absolute top-full left-0 z-30 mt-1 w-full border border-silver-muted/20 bg-ink-charcoal/95 backdrop-blur-md shadow-2xl animate-fade-in">
                            {variant.options.map((option) => (
                              <button
                                key={option}
                                onClick={() => handleSelect(variant.name, option)}
                                className="flex w-full items-center justify-between px-4 py-2.5 text-sm font-light text-ash transition-colors hover:bg-ink-raised hover:text-cream"
                              >
                                <span>{option}</span>
                                {selected === option && <Check className="h-4 w-4 text-gold" strokeWidth={1.5} />}
                              </button>
                            ))}
                          </div>
                        )}
                      </div>
                    </div>
                  );
                })}
              </div>
            )}

            {/* Actions */}
            <div className="mb-4 flex flex-col gap-3 sm:flex-row">
              <button onClick={handleAddToCart} className="btn-gold flex-1">
                <ShoppingBag className="h-4 w-4" strokeWidth={1.5} />
                Add to Cart
              </button>
              <button onClick={() => navigate('contact')} className="btn-outline">
                Request Details
              </button>
            </div>

            {/* Compact tabs */}
            <div className="mt-1 min-h-0 flex-1 border-t border-silver-muted/10">
              {/* Tab headers */}
              <div className="flex gap-1 border-b border-silver-muted/10">
                {tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`flex items-center gap-1.5 px-3 py-2.5 text-xs font-light tracking-wide transition-all duration-300 border-b-2 -mb-px ${
                      activeTab === tab.id
                        ? 'text-gold border-gold'
                        : 'text-ash border-transparent hover:text-cream'
                    }`}
                  >
                    <tab.icon className="h-3.5 w-3.5" strokeWidth={1.5} />
                    {tab.label}
                  </button>
                ))}
              </div>

              {/* Tab content */}
              <div className="py-3 max-h-[180px] overflow-y-auto">
                {activeTab === 'description' && (
                  <p className="text-xs font-light leading-relaxed text-ash animate-fade-in">
                    {product.longDescription}
                  </p>
                )}

                {activeTab === 'specs' && (
                  <div className="divide-y divide-silver-muted/10 animate-fade-in">
                    {product.specs.map((spec) => (
                      <div key={spec.label} className="flex items-center justify-between py-1.5">
                        <span className="text-xs font-light text-silver-muted">{spec.label}</span>
                        <span className="text-xs font-light text-cream">{spec.value}</span>
                      </div>
                    ))}
                  </div>
                )}

                {activeTab === 'accessories' && (
                  <ul className="divide-y divide-silver-muted/10 animate-fade-in">
                    {product.accessories.map((acc, i) => (
                      <li key={i} className="flex items-center justify-between py-1.5">
                        <div className="flex items-center gap-2">
                          <Package className="h-3.5 w-3.5 text-gold/60" strokeWidth={1.5} />
                          <span className="text-xs font-light text-cream">{acc.name}</span>
                        </div>
                        <span className="text-xs font-light text-silver-muted">×{acc.qty}</span>
                      </li>
                    ))}
                  </ul>
                )}

                {activeTab === 'compatibility' && (
                  <p className="text-xs font-light leading-relaxed text-ash animate-fade-in">
                    {product.compatibility}
                  </p>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
