import { createContext, useContext, useState, useEffect, ReactNode, useCallback } from 'react';

export type Route = 'home' | 'about' | 'products' | 'product-details' | 'contact';

interface RouterContextValue {
  route: Route;
  productId: string | null;
  navigate: (route: Route, productId?: string) => void;
}

const RouterContext = createContext<RouterContextValue | undefined>(undefined);

export function RouterProvider({ children }: { children: ReactNode }) {
  const [route, setRoute] = useState<Route>('home');
  const [productId, setProductId] = useState<string | null>(null);

  const navigate = useCallback((newRoute: Route, newProductId?: string) => {
    setRoute(newRoute);
    setProductId(newProductId ?? null);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, []);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [route]);

  return (
    <RouterContext.Provider value={{ route, productId, navigate }}>
      {children}
    </RouterContext.Provider>
  );
}

export function useRouter() {
  const ctx = useContext(RouterContext);
  if (!ctx) throw new Error('useRouter must be used within RouterProvider');
  return ctx;
}
