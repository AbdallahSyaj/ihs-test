import { ArrowRight } from 'lucide-react';
import { useRouter } from '@/router';

export default function HomePage() {
  const { navigate } = useRouter();

  return (
    <div className="relative h-full w-full overflow-hidden animate-fade-in">
      {/* Background image */}
      <div className="absolute inset-0">
        <img
          src="https://images.pexels.com/photos/12683080/pexels-photo-12683080.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop"
          alt="Professional gold detecting in the field"
          className="h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-b from-ink-deep/85 via-ink-deep/55 to-ink-deep/90" />
        <div className="absolute inset-0 bg-gradient-to-r from-ink-deep/75 via-ink-deep/20 to-transparent" />
      </div>

      {/* Content */}
      <div className="container-lux relative z-10 flex h-full items-center">
        <div className="max-w-2xl">
          <div className="section-label mb-5 animate-fade-down">
            Professional Gold Detection Equipment
          </div>
          <h1 className="font-serif text-5xl font-light leading-[1.05] text-cream md:text-6xl lg:text-7xl animate-fade-up" style={{ animationDelay: '100ms' }}>
            Precision Beneath<br />
            <span className="gold-text font-medium">the Surface.</span>
          </h1>
          <p className="mt-6 max-w-lg text-base font-light leading-relaxed text-ash animate-fade-up" style={{ animationDelay: '200ms' }}>
            Professional-grade gold detectors, metal detectors, and prospecting equipment — engineered for those who read the earth with intent.
          </p>
          <div className="mt-8 flex flex-col gap-4 sm:flex-row animate-fade-up" style={{ animationDelay: '300ms' }}>
            <button onClick={() => navigate('products')} className="btn-gold">
              Explore Equipment
              <ArrowRight className="h-4 w-4" strokeWidth={1.5} />
            </button>
            <button onClick={() => navigate('about')} className="btn-outline">
              Discover Our Brand
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
