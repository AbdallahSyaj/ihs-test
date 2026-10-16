import { useState } from 'react';
import { Mail, Phone, MapPin, Clock, Send, Check } from 'lucide-react';

export default function ContactPage() {
  const [form, setForm] = useState({ name: '', email: '', subject: '', message: '' });
  const [submitted, setSubmitted] = useState(false);
  const [errors, setErrors] = useState<Record<string, boolean>>({});

  const handleChange = (field: string, value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }));
    if (errors[field]) setErrors((prev) => ({ ...prev, [field]: false }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const newErrors: Record<string, boolean> = {};
    if (!form.name.trim()) newErrors.name = true;
    if (!form.email.trim()) newErrors.email = true;
    if (!form.subject.trim()) newErrors.subject = true;
    if (!form.message.trim()) newErrors.message = true;

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    setSubmitted(true);
    setForm({ name: '', email: '', subject: '', message: '' });
    setTimeout(() => setSubmitted(false), 5000);
  };

  const contactInfo = [
    { icon: MapPin, label: 'Address', value: '[Store address placeholder]' },
    { icon: Phone, label: 'Phone', value: '[Phone placeholder]' },
    { icon: Mail, label: 'Email', value: '[Email placeholder]' },
    { icon: Clock, label: 'Hours', value: '[Operating hours placeholder]' },
  ];

  return (
    <div className="h-full w-full overflow-hidden animate-fade-in">
      <div className="container-lux grid h-full grid-cols-1 items-center gap-10 py-6 lg:grid-cols-2 lg:gap-16">
        {/* Left: Info */}
        <div className="order-2 lg:order-1">
          <div className="section-label mb-4">Contact Us</div>
          <h1 className="font-serif text-3xl font-light leading-tight text-cream md:text-4xl lg:text-5xl mb-5">
            Let's Find the Right<br />
            <span className="gold-text font-medium">Equipment.</span>
          </h1>
          <p className="text-sm font-light leading-relaxed text-ash mb-6 max-w-md">
            Questions about a product, equipment compatibility, or which detector suits your terrain? Send us a message — we respond with practical, field-informed guidance.
          </p>

          <div className="divider-gold mb-6" />

          <div className="grid grid-cols-2 gap-4">
            {contactInfo.map((item, i) => (
              <div key={i} className="flex items-start gap-3 animate-fade-up" style={{ animationDelay: `${i * 60}ms` }}>
                <div className="flex h-8 w-8 shrink-0 items-center justify-center border border-silver-muted/15 text-gold">
                  <item.icon className="h-3.5 w-3.5" strokeWidth={1.5} />
                </div>
                <div>
                  <dt className="text-[10px] uppercase tracking-ultra-wide text-silver-muted mb-0.5">{item.label}</dt>
                  <dd className="text-xs font-light text-cream">{item.value}</dd>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Right: Form */}
        <div className="order-1 lg:order-2 min-h-0 overflow-y-auto pr-1">
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label className="mb-1.5 block text-[10px] font-medium uppercase tracking-ultra-wide text-silver-muted">Name</label>
                <input
                  type="text"
                  value={form.name}
                  onChange={(e) => handleChange('name', e.target.value)}
                  className={`input-lux ${errors.name ? 'border-red-400/40' : ''}`}
                  placeholder="Your full name"
                />
                {errors.name && <p className="mt-1 text-[10px] text-red-400/80">Please enter your name</p>}
              </div>
              <div>
                <label className="mb-1.5 block text-[10px] font-medium uppercase tracking-ultra-wide text-silver-muted">Email</label>
                <input
                  type="email"
                  value={form.email}
                  onChange={(e) => handleChange('email', e.target.value)}
                  className={`input-lux ${errors.email ? 'border-red-400/40' : ''}`}
                  placeholder="you@example.com"
                />
                {errors.email && <p className="mt-1 text-[10px] text-red-400/80">Please enter a valid email</p>}
              </div>
            </div>

            <div>
              <label className="mb-1.5 block text-[10px] font-medium uppercase tracking-ultra-wide text-silver-muted">Subject</label>
              <input
                type="text"
                value={form.subject}
                onChange={(e) => handleChange('subject', e.target.value)}
                className={`input-lux ${errors.subject ? 'border-red-400/40' : ''}`}
                placeholder="What can we help you with?"
              />
              {errors.subject && <p className="mt-1 text-[10px] text-red-400/80">Please enter a subject</p>}
            </div>

            <div>
              <label className="mb-1.5 block text-[10px] font-medium uppercase tracking-ultra-wide text-silver-muted">Message</label>
              <textarea
                value={form.message}
                onChange={(e) => handleChange('message', e.target.value)}
                rows={4}
                className={`input-lux resize-none ${errors.message ? 'border-red-400/40' : ''}`}
                placeholder="Tell us about the equipment you're considering..."
              />
              {errors.message && <p className="mt-1 text-[10px] text-red-400/80">Please enter your message</p>}
            </div>

            <div className="flex items-center gap-4">
              <button type="submit" className="btn-gold">
                {submitted ? (
                  <>
                    <Check className="h-4 w-4" strokeWidth={1.5} />
                    Message Sent
                  </>
                ) : (
                  <>
                    <Send className="h-4 w-4" strokeWidth={1.5} />
                    Send Message
                  </>
                )}
              </button>
              {submitted && (
                <span className="text-sm font-light text-gold animate-fade-in">
                  Thank you — we'll be in touch shortly.
                </span>
              )}
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
