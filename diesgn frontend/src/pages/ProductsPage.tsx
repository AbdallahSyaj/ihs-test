import { useState, useMemo } from 'react';
import { Search, SlidersHorizontal } from 'lucide-react';
import { products, categories } from '@/data/products';
import ProductCard from '@/components/ProductCard';

export default function ProductsPage() {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState('');

  const filtered = useMemo(() => {
    return products.filter((p) => {
      const matchesCategory = activeCategory === 'all' || p.category === activeCategory;
      const matchesSearch =
        searchQuery === '' ||
        p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        p.model.toLowerCase().includes(searchQuery.toLowerCase()) ||
        p.description.toLowerCase().includes(searchQuery.toLowerCase());
      return matchesCategory && matchesSearch;
    });
  }, [activeCategory, searchQuery]);

  const allCategories = [{ id: 'all', label: 'All Equipment' }, ...categories];

  return (
    <div className="animate-fade-in">
      {/* Page Header */}
      <section className="py-10 lg:py-14">
        <div className="container-lux">
          <div className="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div className="max-w-2xl">
              <div className="section-label mb-4">Our Catalog</div>
              <h1 className="font-serif text-4xl font-light leading-tight text-cream md:text-5xl lg:text-6xl">
                Explore Our Equipment
              </h1>
              <p className="mt-4 text-base font-light leading-relaxed text-ash">
                Professional gold detectors, metal detectors, prospecting gear, and accessories — each selected for field-ready performance and build quality.
              </p>
            </div>

            {/* Search */}
            <div className="w-full lg:w-72">
              <div className="flex items-center gap-3 border border-silver-muted/20 bg-ink-charcoal/50 px-4 py-3 transition-all focus-within:border-gold/50">
                <Search className="h-4 w-4 text-silver-muted shrink-0" strokeWidth={1.5} />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search equipment..."
                  className="flex-1 bg-transparent text-sm font-light text-cream placeholder:text-ash/50 focus:outline-none"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Filters */}
      <section className="border-y border-silver-muted/10 bg-ink-charcoal/30">
        <div className="container-lux py-4">
          <div className="flex items-center gap-4 overflow-x-auto">
            <div className="flex items-center gap-2 text-silver-muted shrink-0">
              <SlidersHorizontal className="h-4 w-4" strokeWidth={1.5} />
              <span className="text-xs uppercase tracking-ultra-wide">Filter</span>
            </div>
            <div className="h-4 w-px bg-silver-muted/15 shrink-0" />
            {allCategories.map((cat) => (
              <button
                key={cat.id}
                onClick={() => setActiveCategory(cat.id)}
                className={`shrink-0 whitespace-nowrap text-sm font-light tracking-wide transition-colors duration-300 ${
                  activeCategory === cat.id
                    ? 'text-gold border-b border-gold pb-1'
                    : 'text-ash hover:text-cream'
                }`}
              >
                {cat.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Product Grid */}
      <section className="py-10 lg:py-14">
        <div className="container-lux">
          <div className="mb-6 flex items-center justify-between">
            <span className="text-sm font-light text-silver-muted">
              {filtered.length} {filtered.length === 1 ? 'item' : 'items'}
            </span>
          </div>

          {filtered.length > 0 ? (
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
              {filtered.map((product, i) => (
                <ProductCard key={product.id} product={product} index={i} />
              ))}
            </div>
          ) : (
            <div className="py-24 text-center">
              <p className="text-sm font-light text-ash">No equipment matches your search.</p>
              <button
                onClick={() => { setActiveCategory('all'); setSearchQuery(''); }}
                className="mt-4 text-sm font-light uppercase tracking-ultra-wide text-gold hover:text-gold-light transition-colors"
              >
                Clear filters
              </button>
            </div>
          )}
        </div>
      </section>
    </div>
  );
}
