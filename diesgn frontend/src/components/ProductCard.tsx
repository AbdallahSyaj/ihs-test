import { Product } from '@/data/products';
import { useRouter } from '@/router';

interface ProductCardProps {
  product: Product;
  index?: number;
}

export default function ProductCard({ product, index = 0 }: ProductCardProps) {
  const { navigate } = useRouter();

  return (
    <button
      onClick={() => navigate('product-details', product.id)}
      className="group flex flex-col overflow-hidden border border-silver-muted/10 bg-ink-charcoal/60 text-left transition-all duration-500 hover:border-gold/30 hover:bg-ink-slate/80 animate-fade-up"
      style={{ animationDelay: `${index * 80}ms` }}
    >
      {/* Image */}
      <div className="relative aspect-[4/3] overflow-hidden bg-ink-slate">
        <img
          src={product.image}
          alt={product.name}
          loading="lazy"
          className="h-full w-full object-cover opacity-90 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-ink-deep/60 via-transparent to-transparent" />
        {product.badge && (
          <span className="absolute top-4 left-4 bg-gold/90 px-3 py-1 text-[10px] font-medium uppercase tracking-ultra-wide text-ink-deep">
            {product.badge}
          </span>
        )}
      </div>

      {/* Info */}
      <div className="flex flex-1 flex-col p-5">
        <span className="text-[10px] font-medium uppercase tracking-ultra-wide text-gold/70 mb-2">
          {product.categoryLabel}
        </span>
        <h3 className="font-serif text-lg font-medium text-cream leading-tight mb-1 transition-colors group-hover:text-gold">
          {product.name}
        </h3>
        <p className="text-[11px] font-light text-silver-muted mb-3">Model {product.model}</p>
        <p className="text-sm font-light leading-relaxed text-ash line-clamp-2 mb-4 flex-1">
          {product.description}
        </p>
        <div className="flex items-center justify-between border-t border-silver-muted/10 pt-4">
          <span className="font-serif text-base font-medium text-cream">{product.price}</span>
          <span className="text-xs font-light uppercase tracking-ultra-wide text-silver-muted transition-colors group-hover:text-gold">
            View Details →
          </span>
        </div>
      </div>
    </button>
  );
}
