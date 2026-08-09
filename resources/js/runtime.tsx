import { createContext, useContext, useEffect, useState, type AnchorHTMLAttributes, type MouseEvent, type ReactNode } from 'react';

type PageState = { props: Record<string, any>; url: string };
const PageContext = createContext<PageState>({ props: {}, url: window.location.pathname });

export function PageProvider({ value, children }: { value: PageState; children: ReactNode }) {
  return <PageContext.Provider value={value}>{children}</PageContext.Provider>;
}

export function usePage<T extends { props?: Record<string, any> } = PageState>() {
  const page = useContext(PageContext);
  return page as T & PageState;
}

export function Head({ title }: { title?: string }) {
  useEffect(() => { if (title) document.title = title; }, [title]);
  return null;
}

export function navigate(href: string) {
  window.history.pushState({}, '', href);
  window.dispatchEvent(new PopStateEvent('popstate'));
}

export function Link({ href, children, method, as, ...props }: AnchorHTMLAttributes<HTMLAnchorElement> & { href: string; method?: string; as?: string }) {
  const handleClick = async (event: MouseEvent<HTMLAnchorElement>) => {
    if (href.startsWith('http') || href.startsWith('tel:') || href.startsWith('mailto:') || href.startsWith('#')) return;
    event.preventDefault();
    if (method) {
      await request(apiPath(href), { method: method.toUpperCase() });
      navigate(href === '/admin/logout' ? '/admin/login' : href);
      return;
    }
    navigate(href);
  };
  const Tag = (as === 'button' ? 'button' : 'a') as any;
  return <Tag {...props} href={as === 'button' ? undefined : href} type={as === 'button' ? 'button' : props.type} onClick={handleClick}>{children}</Tag>;
}

export function useForm<T extends Record<string, any>>(initial: T) {
  const [data, setDataState] = useState<T>(initial);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [processing, setProcessing] = useState(false);
  const setData = (key: keyof T, value: T[keyof T]) => setDataState((current) => ({ ...current, [key]: value }));
  const submit = async (method: string, href: string, options?: { onSuccess?: () => void }) => {
    setProcessing(true); setErrors({});
    try {
      await request(apiPath(href), { method, body: JSON.stringify(data) });
      options?.onSuccess?.();
    } catch (error: any) {
      setErrors(error.errors ?? { form: error.message ?? 'The request could not be completed.' });
    } finally { setProcessing(false); }
  };
  return {
    data,
    errors,
    processing,
    setData,
    reset: () => setDataState(initial),
    post: (href: string, options?: { onSuccess?: () => void }) => submit('POST', href, options),
    patch: (href: string, options?: { onSuccess?: () => void }) => submit('PATCH', href, options),
    delete: (href: string, options?: { onSuccess?: () => void }) => submit('DELETE', href, options),
  };
}

export async function request<T = any>(href: string, options: RequestInit = {}): Promise<T> {
  const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
  const { headers: optionHeaders, ...requestOptions } = options;
  const isFormData = options.body instanceof FormData;
  const response = await fetch(href, {
    ...requestOptions,
    cache: 'no-store',
    credentials: 'same-origin',
    headers: {
      Accept: 'application/json',
      ...(!isFormData ? { 'Content-Type': 'application/json' } : {}),
      ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
      ...(optionHeaders ?? {}),
    },
  });
  const payload = await response.json().catch(() => ({}));
  if (!response.ok) throw payload;
  return payload;
}

export function apiPath(href: string) {
  if (href.startsWith('/api/')) return href;
  if (href === '/admin/login') return '/api/v1/auth/login';
  if (href === '/admin/logout') return '/api/v1/auth/logout';
  if (href === '/admission-enquiries') return '/api/v1/public/admission-enquiries';
  if (href === '/enquiries') return '/api/v1/public/enquiries';
  if (href.startsWith('/admin/')) return `/api/v1${href}`;
  return href;
}
