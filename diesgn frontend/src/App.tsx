import { RouterProvider, useRouter } from '@/router';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import HomePage from '@/pages/HomePage';
import AboutPage from '@/pages/AboutPage';
import ProductsPage from '@/pages/ProductsPage';
import ProductDetailsPage from '@/pages/ProductDetailsPage';
import ContactPage from '@/pages/ContactPage';

function AppContent() {
  const { route } = useRouter();

  const isProducts = route === 'products';

  return (
    <div className={isProducts ? 'min-h-screen bg-ink-deep' : 'h-screen overflow-hidden bg-ink-deep flex flex-col'}>
      <Header />
      <main className={isProducts ? '' : 'flex-1 min-h-0 overflow-hidden'}>
        {route === 'home' && <HomePage />}
        {route === 'about' && <AboutPage />}
        {route === 'products' && <ProductsPage />}
        {route === 'product-details' && <ProductDetailsPage />}
        {route === 'contact' && <ContactPage />}
      </main>
      <Footer />
    </div>
  );
}

function App() {
  return (
    <RouterProvider>
      <AppContent />
    </RouterProvider>
  );
}

export default App;
