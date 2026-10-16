import { ArrowRight } from 'lucide-react';
import { useRouter } from '@/router';

export default function AboutPage() {
  const { navigate } = useRouter();

  return (
    <div className="h-full w-full overflow-hidden animate-fade-in">
      <div className="container-lux grid h-full grid-cols-1 items-center gap-12 py-8 lg:grid-cols-2 lg:gap-16 lg:py-6">
        {/* Text */}
        <div className="order-2 lg:order-1">
          <div className="section-label mb-5">About Aurum Detection Co.</div>
          <h1 className="font-serif text-4xl font-light leading-[1.1] text-cream md:text-5xl lg:text-6xl mb-6">
            Built for the Search.
          </h1>
          <p className="text-base font-light leading-relaxed text-ash mb-5">
            Aurum Detection Co. exists for a specific kind of customer — the prospector who treats detection as a craft, not a hobby. We stock professional gold detectors, metal detectors, and prospecting equipment, and we know how each piece performs in the field.
          </p>
          <p className="text-sm font-light leading-relaxed text-ash/80 mb-6">
            Our focus is narrow by design. By specializing exclusively in detection and prospecting equipment, we offer a level of product knowledge and technical guidance that generalist retailers cannot match.
          </p>
          <div className="divider-gold mb-6" />
          <button onClick={() => navigate('products')} className="text-sm font-light uppercase tracking-ultra-wide text-gold transition-colors hover:text-gold-light flex items-center gap-2 group">
            Explore Equipment
            <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" strokeWidth={1.5} />
          </button>
        </div>

        {/* Image */}
        <div className="relative order-1 lg:order-2 h-full max-h-full min-h-0 overflow-hidden">
          <img
            src="https://images.pexels.com/photos/39826625/pexels-photo-39826625.jpeg?auto=compress&cs=tinysrgb&w=1200&h=1400&fit=crop"
            alt="Prospectors using detection equipment at dawn"
            className="h-full w-full object-cover"
            loading="lazy"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-ink-deep/40 to-transparent lg:bg-gradient-to-l lg:from-ink-deep/30 lg:to-transparent" />
        </div>
      </div>
    </div>
  );
}
