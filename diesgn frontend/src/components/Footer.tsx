import { Crosshair } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="shrink-0 border-t border-silver-muted/10 bg-ink-deep">
      <div className="container-lux flex h-[40px] items-center justify-between">
        <div className="flex items-center gap-2">
          <Crosshair className="h-3.5 w-3.5 text-gold/60" strokeWidth={1.25} />
          <span className="text-[10px] font-light tracking-wide text-silver-muted/60">
            © {new Date().getFullYear()} Aurum Detection Co.
          </span>
        </div>
        <div className="hidden items-center gap-5 sm:flex">
          <span className="text-[10px] font-light uppercase tracking-ultra-wide text-silver-muted/40">Privacy</span>
          <span className="text-[10px] font-light uppercase tracking-ultra-wide text-silver-muted/40">Terms</span>
          <span className="text-[10px] font-light uppercase tracking-ultra-wide text-silver-muted/40">Warranty</span>
        </div>
      </div>
    </footer>
  );
}
