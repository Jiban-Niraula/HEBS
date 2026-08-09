import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { PageProvider, request } from './runtime';
import '../css/app.css';

import AdminDashboard from './pages/Admin/Dashboard';
import AdminLogin from './pages/Admin/Login';
import AdminEnquiries from './pages/Admin/Enquiries/Index';
import AdminContent from './pages/Admin/Content/Index';
import AdminUsers from './pages/Admin/Users/Index';
import Careers from './pages/Public/Careers';
import Contact from './pages/Public/Contact';
import Downloads from './pages/Public/Downloads';
import EventDetail from './pages/Public/EventDetail';
import Events from './pages/Public/Events';
import Facilities from './pages/Public/Facilities';
import Gallery from './pages/Public/Gallery';
import Home from './pages/Public/Home';
import InformationPage from './pages/Public/InformationPage';
import News from './pages/Public/News';
import NewsDetail from './pages/Public/NewsDetail';
import NoticeDetail from './pages/Public/NoticeDetail';
import Notices from './pages/Public/Notices';
import People from './pages/Public/People';
import ProgramDetail from './pages/Public/ProgramDetail';
import Programs from './pages/Public/Programs';

function routeFor(path: string) {
  if (path === '/') return { component: Home, endpoint: '/api/v1/public/home' };
  if (path === '/admin/login') return { component: AdminLogin, endpoint: null };
  if (path === '/admin') return { component: AdminDashboard, endpoint: '/api/v1/admin/dashboard' };
  if (path === '/admin/programs') return { component: AdminContent, endpoint: '/api/v1/admin/content/programs' };
  if (path === '/admin/notices') return { component: AdminContent, endpoint: '/api/v1/admin/content/notices' };
  if (path === '/admin/enquiries') return { component: AdminEnquiries, endpoint: '/api/v1/admin/enquiries' };
  if (path === '/admin/users') return { component: AdminUsers, endpoint: '/api/v1/admin/users' };
  if (path.match(/^\/admin\/content\/[^/]+$/)) return { component: AdminContent, endpoint: `/api/v1${path}` };
  if (path === '/academics') return { component: Programs, endpoint: '/api/v1/public/programs' };
  if (path === '/school-life/facilities') return { component: Facilities, endpoint: '/api/v1/public/facilities' };
  if (path === '/gallery') return { component: Gallery, endpoint: '/api/v1/public/gallery' };
  if (path === '/downloads') return { component: Downloads, endpoint: '/api/v1/public/downloads' };
  if (path === '/contact') return { component: Contact, endpoint: '/api/v1/public/contact' };
  if (path === '/our-school/careers') return { component: Careers, endpoint: '/api/v1/public/careers' };
  if (path === '/updates/notices') return { component: Notices, endpoint: '/api/v1/public/notices' };
  if (path === '/updates/news') return { component: News, endpoint: '/api/v1/public/news' };
  if (path === '/updates/events') return { component: Events, endpoint: '/api/v1/public/events' };
  if (path === '/updates/articles') return { component: InformationPage, endpoint: '/api/v1/public/information/articles' };
  if (path === '/school-life/achievements') return { component: InformationPage, endpoint: '/api/v1/public/information/achievements' };
  if (path === '/admissions/information') return { component: InformationPage, endpoint: '/api/v1/public/information/admission-information' };
  if (path.match(/^\/updates\/notices\/[^/]+$/)) return { component: NoticeDetail, endpoint: `/api/v1/public/notices/${path.split('/').pop()}` };
  if (path.match(/^\/updates\/news\/[^/]+$/)) return { component: NewsDetail, endpoint: `/api/v1/public/news/${path.split('/').pop()}` };
  if (path.match(/^\/updates\/events\/[^/]+$/)) return { component: EventDetail, endpoint: `/api/v1/public/events/${path.split('/').pop()}` };
  if (path === '/academics/academic-calendar') return { component: InformationPage, endpoint: '/api/v1/public/information/academic-calendar' };
  if (path === '/academics/fee-structure') return { component: InformationPage, endpoint: '/api/v1/public/information/fee-structure' };
  if (path.match(/^\/academics\/[^/]+$/)) return { component: ProgramDetail, endpoint: `/api/v1/public/programs/${path.split('/').pop()}` };
  if (path === '/school-life') return { component: InformationPage, endpoint: '/api/v1/public/information/student-life' };
  if (path.match(/^\/school-life\//)) return { component: InformationPage, endpoint: `/api/v1/public/information/${path.split('/').pop()}` };
  if (path.match(/^\/our-school\//)) {
    const slug = path.split('/').pop();
    if (slug === 'teachers' || slug === 'teachers-staff') return { component: People, endpoint: '/api/v1/public/people/staff' };
    if (slug === 'leadership') return { component: People, endpoint: '/api/v1/public/people/leadership' };
    if (slug === 'management-committee') return { component: People, endpoint: '/api/v1/public/people/committee' };
    return { component: InformationPage, endpoint: `/api/v1/public/information/${slug}` };
  }
  if (path.match(/^\/admissions\//)) return { component: InformationPage, endpoint: `/api/v1/public/information/${path.split('/').pop()}` };
  if (path.match(/^\/legal\//)) return { component: InformationPage, endpoint: `/api/v1/public/information/${path.split('/').pop()}` };
  return { component: Home, endpoint: '/api/v1/public/home' };
}

function App() {
  const [path, setPath] = useState(window.location.pathname);
  const [page, setPage] = useState<{ component: React.ComponentType<any>; props: Record<string, any> } | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const onPopState = () => setPath(window.location.pathname);
    window.addEventListener('popstate', onPopState);
    return () => window.removeEventListener('popstate', onPopState);
  }, []);

  useEffect(() => {
    let cancelled = false;
    const load = async () => {
      setError(null); setPage(null);
      try {
        const route = routeFor(path);
        const [base, data] = await Promise.all([
          request(`/api/v1/context?page=${encodeURIComponent(path)}`),
          route.endpoint ? request(route.endpoint) : Promise.resolve({}),
        ]);
        if (cancelled) return;
        setPage({ component: route.component, props: { ...base, ...data } });
      } catch (requestError: any) {
        if (!cancelled) setError(requestError.message ?? 'Unable to load this page.');
      }
    };
    load();
    return () => { cancelled = true; };
  }, [path]);

  if (error) return <div className="app-state"><h1>Unable to load this page</h1><p>{error}</p><button onClick={() => window.location.reload()}>Try again</button></div>;
  if (!page) return <div className="app-state"><span className="app-state-mark">HEBS</span><p>Loading school information...</p></div>;
  const Page = page.component;
  return <PageProvider value={{ props: page.props, url: path }}><Page key={path} {...page.props} /></PageProvider>;
}

createRoot(document.getElementById('app')!).render(<React.StrictMode><App /></React.StrictMode>);
