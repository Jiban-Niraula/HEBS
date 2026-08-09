import { Link, usePage } from '@/runtime';
import { useEffect, useState, type PropsWithChildren } from 'react';
import type { PopupNotice, SchoolIdentity } from '../types/school';

type LandingLayoutProps = PropsWithChildren<{ school: SchoolIdentity; footerOverlap?: boolean }>;
type NavigationItem = { id: number; menu_group: string; parent_label?: string; label: string; href: string; icon?: string; is_primary?: boolean };

export default function LandingLayout({ children, school, footerOverlap = true }: LandingLayoutProps) {
  const { navigation = [], popupNotice = null } = usePage().props as { navigation?: NavigationItem[]; popupNotice?: PopupNotice | null };
  const [scrolled, setScrolled] = useState(false);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [menuOpen, setMenuOpen] = useState(false);
  const [expanded, setExpanded] = useState<string | null>(null);
  const [popupOpen, setPopupOpen] = useState(false);
  const [doNotShowAgain, setDoNotShowAgain] = useState(false);
  const email = school.email || 'info@hamroenglishschool.edu.np';
  const phone = school.phone || '+977-25-000000';
  const hours = school.officeHours || 'Sunday-Friday, 9:30 AM-4:30 PM';
  const headerItems = navigation.filter((item) => item.menu_group === 'header');
  const primaryHeaderItems = headerItems.filter((item) => item.is_primary);
  const primaryNavigation = primaryHeaderItems.map((primary) => ({ primary, children: headerItems.filter((item) => !item.is_primary && item.parent_label === primary.label) }));
  const footerInstitution = navigation.filter((item) => item.menu_group === 'footer_institution');
  const footerAcademics = navigation.filter((item) => item.menu_group === 'footer_academics');
  const logo = school.logoUrl || '/images/hebs-facebook-profile.jpg';

  useEffect(() => {
    const updateHeader = () => {
      setScrolled(window.scrollY > 40);
      const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
      setScrollProgress(scrollableHeight > 0 ? Math.min(100, (window.scrollY / scrollableHeight) * 100) : 0);
    };
    const closeOnEscape = (event: KeyboardEvent) => event.key === 'Escape' && setMenuOpen(false);
    updateHeader();
    document.documentElement.classList.add('landing-scroll-hidden');
    window.addEventListener('scroll', updateHeader, { passive: true });
    document.addEventListener('keydown', closeOnEscape);
    document.body.style.overflow = menuOpen ? 'hidden' : '';
    return () => {
      window.removeEventListener('scroll', updateHeader);
      document.removeEventListener('keydown', closeOnEscape);
      document.body.style.overflow = '';
      document.documentElement.classList.remove('landing-scroll-hidden');
    };
  }, [menuOpen]);

  useEffect(() => setPopupOpen(shouldShowPopup(popupNotice)), [popupNotice]);

  const closePopup = () => {
    if (popupNotice && (popupNotice.displayFrequency !== 'every_visit' || doNotShowAgain)) {
      const storage = popupNotice.displayFrequency === 'once_per_session' && !doNotShowAgain ? sessionStorage : localStorage;
      storage.setItem(noticeStorageKey(popupNotice), 'seen');
    }
    setPopupOpen(false);
  };

  return (
    <div className="nl-shell min-w-[320px] overflow-x-hidden bg-brand-surface font-sans text-gray-700 antialiased">
      <div className={`fixed top-0 z-[110] hidden w-full overflow-hidden bg-brand-dark px-6 text-[15px] text-gray-300 transition-all duration-500 lg:block ${scrolled ? 'max-h-0 py-0 opacity-0' : 'max-h-12 py-2 opacity-100'}`}>
        <div className="mx-auto flex max-w-[1400px] items-center justify-between">
          <div className="flex items-center space-x-6 font-medium">
            <a className="flex items-center transition hover:text-brand-accent" href={`mailto:${email}`}><i className="ph-fill ph-envelope-simple mr-2 text-[17px] text-brand-accent" />{email}</a>
            <a className="flex items-center transition hover:text-brand-accent" href={`tel:${phone}`}><i className="ph-fill ph-phone mr-2 text-[17px] text-brand-accent" />{phone}</a>
            <span className="flex items-center"><i className="ph-fill ph-clock mr-2 text-[17px] text-brand-accent" />{hours}</span>
          </div>
          <a className="flex items-center font-medium transition hover:text-white" href={school.appUrl || '#'}><i className="ph-fill ph-device-mobile mr-1.5 text-[17px] text-brand-accent" />Download App</a>
        </div>
      </div>

      <header className={`fixed z-[100] w-full transition-all duration-500 ${scrolled ? 'top-0 py-2' : 'top-0 py-3 lg:top-[36px] lg:py-4'}`}>
        <div className="relative mx-auto max-w-[1400px] px-3 sm:px-4 lg:px-6">
          <div className={`flex h-[68px] items-center justify-between rounded-2xl bg-white px-3 shadow-glass transition-all duration-300 sm:h-[72px] sm:px-4 lg:h-[86px] lg:px-6 ${scrolled ? 'shadow-float' : ''}`}>
            <Link className="relative z-20 flex shrink-0 items-center space-x-2 lg:space-x-3" href="/">
              <span className="h-11 w-11 overflow-hidden rounded-xl bg-brand-900 sm:h-12 sm:w-12 lg:h-14 lg:w-14"><img className="h-full w-full object-cover" src={logo} alt={`${school.name} logo`} /></span>
              <span className="min-w-0"><strong className="block whitespace-nowrap text-[12px] font-black leading-tight text-brand-900 min-[375px]:text-[15px] sm:text-[17px] lg:text-[20px]">{school.name}</strong><small className="mt-1 block whitespace-nowrap text-[8px] font-bold text-brand-accent min-[375px]:text-[10px] sm:text-[11px] lg:text-[12px]">{school.address}</small></span>
            </Link>

            <nav className="relative z-20 hidden h-full items-center space-x-1 lg:flex xl:space-x-3" aria-label="Main navigation">
              {primaryNavigation.map(({ primary, children: menuItems }) => menuItems.length ? (
                <div className="group relative flex h-full items-center" key={primary.id}>
                  <button className="flex items-center rounded-lg border-0 bg-transparent px-3 py-2 font-bold text-gray-600 outline-none transition hover:bg-gray-50 hover:text-brand-900 focus-visible:ring-2 focus-visible:ring-brand-accent" type="button">{primary.label}<i className="ph-bold ph-caret-down ml-1 text-[10px] transition-transform group-hover:rotate-180 group-hover:text-brand-accent" /></button>
                  <div className={`invisible absolute top-full z-50 pt-2 opacity-0 transition duration-200 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 ${primary.label === 'Levels' ? 'right-0 w-60' : 'left-1/2 w-56 -translate-x-1/2'}`}>
                    <div className="flex flex-col rounded-2xl border border-gray-100 bg-white p-2 shadow-float">
                      {menuItems.map((item) => <Link className="rounded-xl px-4 py-2.5 font-bold text-gray-600 transition hover:bg-brand-surface hover:text-brand-accent" href={item.href} key={item.id}>{item.label}</Link>)}
                    </div>
                  </div>
                </div>
              ) : <Link className="rounded-lg px-3 py-2 font-bold text-gray-600 transition hover:bg-gray-50 hover:text-brand-900" href={primary.href} key={primary.id}>{primary.label}</Link>)}
            </nav>

            <button className="relative z-[120] flex h-10 w-10 items-center justify-center rounded-xl border-0 bg-brand-surface text-brand-900 outline-none transition hover:bg-gray-200 focus-visible:ring-2 focus-visible:ring-brand-accent lg:hidden" type="button" onClick={() => setMenuOpen(true)} aria-label="Open menu"><i className="ph-bold ph-list text-xl" /></button>
          </div>
        </div>
      </header>

      {menuOpen ? (
        <div className="fixed inset-0 z-[130] lg:hidden">
          <button className="absolute inset-0 h-full w-full border-0 bg-brand-dark/60 backdrop-blur-sm" type="button" onClick={() => setMenuOpen(false)} aria-label="Close menu" />
          <aside className="absolute inset-y-0 left-0 w-[85vw] max-w-[380px] overflow-y-auto bg-white shadow-2xl">
            <div className="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-gray-100 bg-white/95 px-5 backdrop-blur-xl">
              <div className="flex min-w-0 items-center gap-3"><img className="h-10 w-10 shrink-0 rounded-xl object-cover" src={logo} alt={`${school.name} logo`} /><div className="min-w-0"><strong className="block text-[13px] font-black leading-tight text-brand-900">{school.name}</strong><small className="text-[9px] font-bold text-brand-accent">{school.address}</small></div></div>
              <button className="flex h-10 w-10 items-center justify-center rounded-xl border-0 bg-gray-100 text-brand-900 outline-none focus-visible:ring-2 focus-visible:ring-brand-accent" type="button" onClick={() => setMenuOpen(false)} aria-label="Close menu"><i className="ph-bold ph-x text-lg" /></button>
            </div>
            <nav className="space-y-1 px-4 py-4" aria-label="Mobile navigation">
              {primaryNavigation.map(({ primary, children: menuItems }) => menuItems.length ? (
                <div className="overflow-hidden rounded-2xl" key={primary.id}>
                  <button className="flex w-full items-center justify-between rounded-xl border-0 bg-white px-4 py-3.5 text-left font-bold text-brand-900 outline-none transition hover:bg-gray-50 focus-visible:ring-2 focus-visible:ring-brand-accent" type="button" onClick={() => setExpanded(expanded === primary.label ? null : primary.label)} aria-expanded={expanded === primary.label}>{primary.label === 'Levels' ? 'Academic Levels' : primary.label}<i className={`ph-bold ${expanded === primary.label ? 'ph-caret-up' : 'ph-caret-down'}`} /></button>
                  {expanded === primary.label ? <div className="mx-2 mb-2 space-y-1 rounded-xl bg-gray-50 px-4 py-3">{menuItems.map((item) => <Link className="block rounded-lg px-3 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-white hover:text-brand-900" href={item.href} key={item.id} onClick={() => setMenuOpen(false)}>{item.label}</Link>)}</div> : null}
                </div>
              ) : <Link className="flex items-center justify-between rounded-xl bg-brand-surface px-4 py-3.5 font-bold text-brand-900" href={primary.href} onClick={() => setMenuOpen(false)} key={primary.id}>{primary.label}<i className={`ph-bold ${primary.icon || 'ph-arrow-right'} text-lg`} /></Link>)}
            </nav>
          </aside>
        </div>
      ) : null}

      {children}

      <button
        className={`fixed bottom-5 right-4 z-[120] flex h-14 w-14 items-center justify-center rounded-full border-0 p-[3px] shadow-2xl outline-none transition duration-300 focus-visible:ring-2 focus-visible:ring-brand-accent focus-visible:ring-offset-2 sm:bottom-7 sm:right-7 ${scrolled ? 'translate-y-0 opacity-100' : 'pointer-events-none translate-y-3 opacity-0'}`}
        style={{ background: `conic-gradient(#d39a2c ${scrollProgress}%, rgba(255,255,255,.22) ${scrollProgress}% 100%)` }}
        type="button"
        onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
        aria-label={`Back to top, ${Math.round(scrollProgress)}% page viewed`}
        title="Back to top"
      >
        <span className="flex h-full w-full items-center justify-center rounded-full bg-brand-dark text-white transition hover:bg-brand-900"><i className="ph-bold ph-arrow-up text-xl" /></span>
      </button>

      <footer id="contact" className={`relative z-10 scroll-mt-24 overflow-hidden rounded-t-[2rem] bg-brand-dark pb-8 text-sm text-gray-400 sm:rounded-t-[3rem] sm:pb-12 lg:rounded-t-[4rem] ${footerOverlap ? 'pt-36 sm:pt-44 lg:pt-56' : 'pt-16 sm:pt-20 lg:pt-24'}`}>
        <div className="absolute left-0 right-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-accent/60 to-transparent" />
        <div className="mx-auto grid max-w-[1400px] grid-cols-1 gap-10 px-4 sm:gap-12 sm:px-6 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">
          <div className="lg:col-span-4 lg:pr-8">
            <Link className="mb-5 flex items-center gap-4 sm:mb-6" href="/">
              <img className="h-14 w-14 shrink-0 rounded-xl bg-white object-cover p-1 shadow-lg sm:h-16 sm:w-16" src={logo} alt={`${school.name} logo`} />
              <span className="min-w-0"><strong className="block text-lg font-black leading-tight text-white sm:text-xl">{school.name}</strong><small className="mt-1 flex items-center gap-1.5 text-[10px] font-bold text-brand-accent sm:text-xs"><i className="ph-fill ph-map-pin" />{school.address}</small></span>
            </Link>
            <p className="mb-6 max-w-md text-sm font-medium leading-7 text-gray-400">{school.motto}</p>
            <p className="mb-3 text-[10px] font-black uppercase text-gray-500">Connect with us</p>
            <div className="flex gap-3">
              {Object.entries(school.socialLinks ?? {}).filter(([, href]) => href).map(([network, href]) => <a className="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white shadow-sm transition hover:-translate-y-0.5 hover:border-brand-accent hover:bg-brand-accent hover:text-brand-900" href={href} target="_blank" rel="noreferrer" aria-label={network} title={network} key={network}><i className={`ph-fill ph-${network}-logo text-xl`} /></a>)}
            </div>
          </div>

          <FooterLinks title="The Institution" icon="ph-buildings" links={footerInstitution} />
          <FooterLinks title="Academics & Info" icon="ph-graduation-cap" links={footerAcademics} />

          <div className="h-fit rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-2xl sm:p-6 lg:col-span-4">
            <div className="mb-5 flex items-center justify-between"><h4 className="flex items-center gap-2 text-xs font-black uppercase text-white"><i className="ph-fill ph-phone-call text-lg text-brand-accent" />Reach Out</h4><span className="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,.8)]" title="Office contact available" /></div>
            <div className="space-y-2">
              <ContactRow icon="ph-map-pin" label="Visit us" value={school.address || 'Itahari, Sunsari, Nepal'} />
              <ContactRow icon="ph-phone" label="Call" value={phone} href={`tel:${phone}`} />
              <ContactRow icon="ph-envelope-simple" label="Email" value={email} href={`mailto:${email}`} />
              <ContactRow icon="ph-clock" label="Office hours" value={hours} />
            </div>
            <div className="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg"><iframe className="h-40 w-full border-0 sm:h-44" title="School location map" src={school.mapUrl} loading="lazy" referrerPolicy="no-referrer-when-downgrade" /></div>
          </div>
        </div>
        <div className="mx-auto mt-12 max-w-[1400px] px-4 sm:mt-16 sm:px-6">
          <div className="flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-7 text-center text-[11px] font-bold uppercase text-gray-500 md:flex-row md:text-left">
            <p className="flex items-center gap-2"><i className="ph ph-copyright text-base text-brand-accent" />{school.copyrightText || `${new Date().getFullYear()} ${school.name}. All Rights Reserved.`}</p>
            <div className="flex items-center gap-5 md:gap-6"><Link className="flex items-center gap-1.5 transition hover:text-white" href="/legal/privacy"><i className="ph ph-shield-check text-base text-brand-accent" />Privacy Policy</Link><Link className="flex items-center gap-1.5 transition hover:text-white" href="/legal/terms"><i className="ph ph-file-text text-base text-brand-accent" />Terms of Service</Link></div>
          </div>
        </div>
      </footer>

      {popupOpen && popupNotice ? <div className="popup-overlay" role="presentation" onMouseDown={popupNotice.showCloseButton === false ? undefined : closePopup}><section className={`popup-dialog ${popupNotice.priority}`} role="dialog" aria-modal="true" aria-labelledby="popup-title" onMouseDown={(event) => event.stopPropagation()}>{popupNotice.imageUrl ? <img src={popupNotice.imageUrl} alt="" /> : null}{popupNotice.showCloseButton !== false ? <button className="popup-close" type="button" onClick={closePopup} aria-label="Close notice"><i className="ph-bold ph-x" /></button> : null}<span className="mb-3 block text-xs font-black uppercase text-brand-accent">School notice</span><h2 id="popup-title">{popupNotice.title}</h2><p>{popupNotice.summary || popupNotice.content}</p>{popupNotice.allowDoNotShowAgain ? <label className="popup-dismiss-option"><input type="checkbox" checked={doNotShowAgain} onChange={(event) => setDoNotShowAgain(event.target.checked)} />Do not show this notice again</label> : null}<div className="popup-actions">{popupNotice.buttonUrl ? <Link className="button primary" href={popupNotice.buttonUrl}>{popupNotice.buttonLabel || 'Read notice'}</Link> : null}{popupNotice.showCloseButton !== false ? <button className="button secondary" type="button" onClick={closePopup}>Close</button> : null}</div></section></div> : null}
    </div>
  );
}

function noticeStorageKey(notice: PopupNotice) {
  if (notice.displayFrequency === 'once_per_day') return `hebs-popup-${notice.id}-${new Date().toDateString()}`;
  return `hebs-popup-${notice.id}`;
}

function shouldShowPopup(notice?: PopupNotice | null) {
  if (!notice || notice.displayFrequency === 'every_visit') return Boolean(notice);
  const storage = notice.displayFrequency === 'once_per_session' ? sessionStorage : localStorage;
  return !storage.getItem(noticeStorageKey(notice));
}

function FooterLinks({ title, icon, links }: { title: string; icon: string; links: NavigationItem[] }) {
  return <div className="lg:col-span-2"><h4 className="mb-5 flex items-center gap-2 text-xs font-black uppercase text-white sm:mb-6"><i className={`ph-fill ${icon} text-lg text-brand-accent`} />{title}</h4><ul className="m-0 list-none space-y-2 p-0 font-semibold">{links.map((item) => <li key={item.id}><Link className="group flex items-center gap-3 rounded-lg py-2 text-gray-400 transition hover:text-white" href={item.href}><span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-brand-accent transition group-hover:bg-brand-accent group-hover:text-brand-900"><i className={`ph ${item.icon || 'ph-caret-right'} text-base`} /></span><span>{item.label}</span></Link></li>)}</ul></div>;
}

function ContactRow({ icon, label, value, href }: { icon: string; label: string; value: string; href?: string }) {
  const content = <><span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 text-brand-accent"><i className={`ph-fill ${icon} text-lg`} /></span><span className="min-w-0"><small className="mb-0.5 block text-[9px] font-black uppercase text-gray-500">{label}</small><strong className="block break-words text-[13px] font-semibold leading-snug text-gray-200">{value}</strong></span></>;
  return href ? <a className="flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/5" href={href}>{content}</a> : <div className="flex items-center gap-3 rounded-xl p-2">{content}</div>;
}
