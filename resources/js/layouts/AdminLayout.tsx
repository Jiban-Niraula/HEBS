import { Link, usePage } from '@/runtime';
import { useState, type PropsWithChildren } from 'react';

const groups = [
  { label: 'Overview', items: [{ label: 'Dashboard', href: '/admin', icon: 'ph-squares-four' }] },
  { label: 'Website', items: [
    { label: 'School identity', href: '/admin/content/settings', icon: 'ph-buildings' },
    { label: 'Hero carousel', href: '/admin/content/hero-slides', icon: 'ph-images-square' },
    { label: 'Navigation', href: '/admin/content/navigation', icon: 'ph-list-dashes' },
    { label: 'Pages & sections', href: '/admin/content/pages', icon: 'ph-file-text' },
  ] },
  { label: 'Publishing', items: [
    { label: 'Programs & syllabus', href: '/admin/content/programs', icon: 'ph-books' },
    { label: 'People directory', href: '/admin/content/people', icon: 'ph-users-three' },
    { label: 'Careers', href: '/admin/content/careers', icon: 'ph-briefcase' },
    { label: 'Notices', href: '/admin/content/notices', icon: 'ph-bell-ringing' },
    { label: 'News & articles', href: '/admin/content/news', icon: 'ph-newspaper' },
    { label: 'Events', href: '/admin/content/events', icon: 'ph-calendar-dots' },
    { label: 'Facilities', href: '/admin/content/facilities', icon: 'ph-buildings' },
    { label: 'Gallery albums', href: '/admin/content/gallery', icon: 'ph-images' },
    { label: 'Downloads', href: '/admin/content/documents', icon: 'ph-download-simple' },
    { label: 'Popup ads', href: '/admin/content/popups', icon: 'ph-notification' },
  ] },
  { label: 'Operations', items: [
    { label: 'Enquiries', href: '/admin/enquiries', icon: 'ph-chats-circle' },
    { label: 'Admin users', href: '/admin/users', icon: 'ph-user-gear', administratorOnly: true },
  ] },
];

export default function AdminLayout({ children }: PropsWithChildren) {
  const { url, props } = usePage();
  const adminUser = props.adminUser as { name?: string; email?: string; role?: string } | undefined;
  const initials = (adminUser?.name || 'Admin').split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase();
  const [mobileNavigationOpen, setMobileNavigationOpen] = useState(false);

  return <div className="admin-shell">
    <aside className="admin-sidebar">
      <div className="admin-mobile-brand-row"><Link className="admin-brand" href="/admin"><span className="admin-brand-mark"><img src="/images/hebs-facebook-profile.jpg" alt="HEBS logo" /></span><span><strong>HEBS CMS</strong><small>School administration</small></span></Link><button className="admin-mobile-menu" type="button" onClick={() => setMobileNavigationOpen((open) => !open)} aria-label="Toggle CMS navigation" aria-expanded={mobileNavigationOpen}><i className={`ph-bold ${mobileNavigationOpen ? 'ph-x' : 'ph-list'}`} /></button></div>
      <nav className={mobileNavigationOpen ? 'is-open' : ''} aria-label="Administration navigation">{groups.map((group) => <section className="admin-nav-group" key={group.label}><div className="admin-nav-label">{group.label}</div>{group.items.filter((item) => !('administratorOnly' in item) || adminUser?.role === 'administrator').map((item) => <Link className={url === item.href || (item.href !== '/admin' && url.startsWith(item.href)) ? 'is-active' : ''} href={item.href} key={item.href} onClick={() => setMobileNavigationOpen(false)}><span className="admin-nav-icon" aria-hidden="true"><i className={`ph-fill ${item.icon}`} /></span><span>{item.label}</span></Link>)}</section>)}</nav>
      <div className="admin-sidebar-footer"><Link className="admin-external-link" href="/"><i className="ph-bold ph-arrow-square-out" /><span>Open public website</span></Link><Link as="button" method="post" href="/admin/logout"><i className="ph-bold ph-sign-out" />Sign out</Link></div>
    </aside>
    <div className="admin-main">
      <header className="admin-topbar"><div><strong>Content management</strong><span>Hamro English Boarding School</span></div><div className="admin-topbar-actions"><Link className="admin-view-site" href="/" title="Open public website"><i className="ph ph-arrow-square-out" /></Link><span className="admin-avatar">{initials}</span><span className="admin-user-name"><strong>{adminUser?.name || 'Administrator'}</strong><small>{adminUser?.role?.replace('_', ' ') || 'administrator'}</small></span></div></header>
      <main>{children}</main>
    </div>
  </div>;
}
