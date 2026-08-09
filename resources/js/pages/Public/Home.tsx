import { Head, Link, usePage } from '@/runtime';
import { useEffect, useState } from 'react';
import LandingLayout from '../../layouts/LandingLayout';
import type { NewsSummary, NoticeSummary, SchoolIdentity } from '../../types/school';

type GallerySummary = { name: string; category?: string; description?: string; cover?: string | null; href?: string };
type HeroSlide = { image: string; kicker: string; title: string; text: string };
type AcademicCard = { key: string; tab: string; step: string; title: string; route: string; image: string; description: string; focus: string; items: string[] };
type HomeSection = { title: string; summary?: string; content?: string; image?: string | null; sections?: Array<{ title: string; body: string }> };
type HomeProps = {
  school: SchoolIdentity;
  notices?: NoticeSummary[];
  news?: NewsSummary[];
  gallery?: GallerySummary[];
  heroSlides?: HeroSlide[];
  programs?: AcademicCard[];
  homeSections?: Record<string, HomeSection>;
};

export default function Home({ school, notices: noticeData, news: newsData, gallery, heroSlides, programs = [], homeSections = {} }: HomeProps) {
  const { navigation = [] } = usePage().props as { navigation?: Array<{ id: number; menu_group: string; parent_label?: string; label: string; href: string; icon?: string }> };
  const [heroIndex, setHeroIndex] = useState(0);
  const [activeAcademic, setActiveAcademic] = useState(programs[0]?.key ?? '');
  const notices = noticeData?.slice(0, 3) ?? [];
  const news = newsData?.slice(0, 2) ?? [];
  const slides = heroSlides?.length ? heroSlides : [{ image: '/images/school-hero.jpg', kicker: '', title: '', text: '' }];
  const academic = programs.find((item) => item.key === activeAcademic) || programs[0];
  const about = homeSections['home-about'];
  const gallerySection = homeSections['home-gallery'];
  const admissions = homeSections['home-admissions'];
  const updates = homeSections['home-updates'];
  const academicsSection = homeSections['home-academics'];
  const quickActions = navigation.filter((item) => item.menu_group === 'quick_action');

  useEffect(() => {
    if (slides.length < 2) return;
    const timer = window.setInterval(() => setHeroIndex((current) => (current + 1) % slides.length), 5000);
    return () => window.clearInterval(timer);
  }, [slides.length]);

  useEffect(() => {
    if (!programs.some((program) => program.key === activeAcademic)) setActiveAcademic(programs[0]?.key ?? '');
  }, [programs, activeAcademic]);

  useEffect(() => {
    if (!window.location.hash) return;
    window.requestAnimationFrame(() => document.querySelector(window.location.hash)?.scrollIntoView());
  }, []);

  return (
    <LandingLayout school={school}>
      <Head title="HEBS - A Steady Foundation" />
      <main>
        <section id="home" className="relative min-h-[100svh] w-full bg-brand-dark md:h-[100svh]">
          <div className="absolute inset-0 overflow-hidden">
            {slides.map((slide, index) => <img className={`absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ${heroIndex === index ? 'opacity-100' : 'opacity-0'}`} src={slide.image} alt="" key={`${slide.image}-${index}`} />)}
            <div className="absolute inset-0 bg-brand-900/60 mix-blend-multiply" />
            <div className="absolute inset-0 bg-gradient-to-b from-brand-900/40 via-transparent to-brand-900/90" />
          </div>
          <div className="pointer-events-none absolute inset-0 z-20 mt-12 flex items-center justify-center px-4 sm:px-6 md:mt-16 lg:mt-0"><div className="max-w-5xl text-center">{slides[heroIndex]?.kicker ? <span className="mb-4 block text-xs font-black uppercase text-brand-accent sm:text-sm">{slides[heroIndex].kicker}</span> : null}<h1 className="text-3xl font-black leading-[1.1] text-white drop-shadow-2xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-[5rem]">{slides[heroIndex]?.title}</h1>{slides[heroIndex]?.text ? <p className="mx-auto mt-5 max-w-2xl text-sm font-semibold leading-relaxed text-white/85 sm:text-lg">{slides[heroIndex].text}</p> : null}</div></div>

          <div className="absolute bottom-16 left-0 right-0 z-[50] mx-auto max-w-5xl translate-y-1/2 px-3 sm:bottom-20 sm:px-6 lg:bottom-24">
            <div className="grid grid-cols-4 items-center gap-1 rounded-2xl border border-gray-100 bg-white/90 p-2 shadow-float backdrop-blur-xl sm:gap-2 sm:rounded-[2rem] sm:p-3 lg:gap-4 lg:p-4">
              {quickActions.slice(0, 4).map((item, index) => <ParentAction href={item.href} icon={item.icon || 'ph-arrow-right'} tone={(['red', 'navy', 'blue', 'green'] as const)[index]} title={item.label} subtitle={item.parent_label || ''} key={item.id} />)}
            </div>
          </div>
        </section>

        <section id="updates" className="relative z-10 scroll-mt-24 border-b border-gray-200 bg-brand-surface pb-16 pt-20 sm:pb-24 sm:pt-24 lg:pt-32">
          <div className="mx-auto max-w-[1400px] px-4 sm:px-6">
            <div className="grid items-stretch gap-8 lg:grid-cols-12 lg:gap-12">
              <section className="flex h-full flex-col lg:col-span-4">
                <span className="mb-2 block text-xs font-bold uppercase text-brand-muted">{updates?.summary}</span>
                <div className="mb-4 flex items-end justify-between sm:mb-6"><h2 className="text-2xl font-black text-brand-900 sm:text-3xl">{updates?.title}</h2><Link className="text-sm font-bold text-brand-900 transition hover:text-brand-accent" href="/updates/notices">View all</Link></div>
                <div className="flex-1 overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-soft">
                  <div className="flex items-center bg-brand-red px-4 py-3 text-base font-bold text-white sm:px-6 sm:py-4 sm:text-lg"><i className="ph-fill ph-push-pin mr-3" />Latest Updates</div>
                  <div className="divide-y divide-gray-100">{notices.map((notice) => <Link className="group block p-4 transition hover:bg-gray-50 sm:p-6" href={notice.href || '/updates/notices'} key={`${notice.date}-${notice.title}`}><div className="mb-2 flex items-start justify-between gap-3"><span className="rounded bg-red-50 px-2 py-0.5 text-[10px] font-black uppercase text-brand-red">{notice.category}</span><time className="text-xs font-semibold text-gray-400">{notice.date}</time></div><h3 className="text-[15px] font-bold leading-snug text-brand-900 transition group-hover:text-brand-accent sm:text-[16px]">{notice.title}</h3></Link>)}</div>
                </div>
              </section>

              <section className="flex h-full flex-col lg:col-span-8">
                <span className="mb-2 block text-xs font-bold uppercase text-brand-muted">{updates?.sections?.[0]?.body}</span>
                <div className="mb-4 flex items-end justify-between sm:mb-6"><h2 className="text-2xl font-black text-brand-900 sm:text-3xl">{updates?.sections?.[0]?.title}</h2><Link className="text-sm font-bold text-brand-900 transition hover:text-brand-accent" href="/updates/news">View all</Link></div>
                <div className="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">{news.map((item) => <article className="group flex flex-col overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-soft" key={`${item.date}-${item.title}`}><div className="relative h-40 overflow-hidden sm:h-48"><img className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src={item.image || '/images/school-hero.jpg'} alt="" /><span className="absolute left-4 top-4 rounded-lg bg-white/90 px-3 py-1 text-[10px] font-black uppercase text-brand-accent backdrop-blur">{item.category || 'School News'}</span></div><div className="flex flex-1 flex-col p-4 sm:p-6"><h3 className="mb-2 text-lg font-bold leading-tight text-brand-900 transition group-hover:text-brand-accent sm:mb-3 sm:text-xl">{item.title}</h3><p className="mb-4 flex-1 text-sm text-gray-500">{item.summary}</p><Link className="inline-flex items-center text-sm font-bold text-brand-900 hover:text-brand-accent" href={item.href || '/updates/news'}>Read Article<i className="ph-bold ph-arrow-right ml-1" /></Link></div></article>)}</div>
              </section>
            </div>
          </div>
        </section>

        <section id="about" className="scroll-mt-24 bg-white py-16 sm:py-24">
          <div className="mx-auto max-w-[1400px] px-4 sm:px-6"><div className="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div><h2 className="mb-4 text-center text-3xl font-black text-brand-900 sm:mb-6 sm:text-4xl lg:text-5xl">{about?.title}</h2><p className="mb-4 text-lg font-bold leading-snug text-brand-900 sm:mb-6 sm:text-xl">{about?.summary}</p><p className="mb-6 text-base font-medium leading-relaxed text-gray-500 sm:mb-8 sm:text-lg">{about?.content}</p><Link className="mb-8 flex items-center text-base font-bold text-brand-900 transition hover:text-brand-accent sm:mb-12 sm:text-lg" href="/our-school/about">Read about our school<i className="ph-bold ph-arrow-right ml-2" /></Link><div className="grid grid-cols-3 gap-4 border-t border-gray-200 pt-6 sm:gap-8 sm:pt-8">{(about?.sections ?? []).map((fact) => <Fact label={fact.title} value={fact.body} key={fact.title} />)}</div></div>
            <div className="relative mt-8 lg:mt-0"><img className="h-[300px] w-full rounded-[2rem] object-cover shadow-float sm:h-[400px] sm:rounded-[3rem] lg:h-[500px]" src={about?.image || '/images/school-hero.jpg'} alt="Hamro English Boarding School campus" /><div className="absolute -bottom-6 -left-6 hidden h-40 w-40 items-center justify-center rounded-full border-8 border-white bg-brand-surface text-center shadow-sm md:flex"><div><span className="block text-3xl font-black text-brand-accent">{school.establishedYear}</span><span className="mt-1 block text-[10px] font-bold uppercase text-brand-900">Established<br />with purpose</span></div></div></div>
          </div></div>
        </section>

        <section id="academics" className="scroll-mt-24 bg-white py-16 sm:py-24">
          <div className="mx-auto mb-8 max-w-[1000px] px-4 text-center sm:mb-12 sm:px-6"><span className="mb-3 block text-xs font-black uppercase text-brand-accent">{academicsSection?.summary}</span><h2 className="text-3xl font-black text-brand-900 sm:text-4xl lg:text-5xl">{academicsSection?.title}</h2></div>
          <div className="mx-auto max-w-[1200px] px-4 sm:px-6">
            <div className="mx-auto mb-8 grid w-full grid-cols-2 gap-1.5 rounded-2xl border border-gray-200 bg-brand-surface p-1.5 shadow-sm sm:mb-12 sm:flex sm:w-fit sm:flex-wrap sm:justify-center sm:gap-2 sm:p-2">{programs.map((item) => <button className={`min-w-0 whitespace-nowrap rounded-lg border-0 px-3 py-2.5 text-xs font-bold outline-none transition-all focus-visible:ring-2 focus-visible:ring-brand-accent sm:rounded-xl sm:px-6 sm:py-3 sm:text-sm ${activeAcademic === item.key ? 'bg-brand-900 text-white shadow-md' : 'bg-transparent text-gray-500 hover:bg-gray-200 hover:text-brand-900'}`} type="button" onClick={() => setActiveAcademic(item.key)} key={item.key}>{item.tab}</button>)}</div>
            {academic ? <div className="grid min-h-[400px] grid-cols-1 items-center gap-8 lg:min-h-[500px] lg:grid-cols-2 lg:gap-12"><div className="aspect-[4/3] w-full overflow-hidden rounded-2xl shadow-float sm:rounded-3xl lg:aspect-square"><img className="h-full w-full object-cover" src={academic.image} alt={academic.title} /></div><div className="py-2 sm:py-4"><span className="mb-4 inline-block rounded-lg border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-black text-brand-900">{academic.step}</span><h3 className="mb-3 text-2xl font-black text-brand-900 sm:mb-4 sm:text-3xl lg:text-4xl">{academic.title}</h3><p className="mb-6 text-base font-medium leading-relaxed text-gray-500 sm:mb-8 sm:text-lg">{academic.description}</p><div className="mb-6 grid grid-cols-1 gap-4 sm:mb-8 sm:grid-cols-2 sm:gap-6"><div className="rounded-2xl border border-gray-100 bg-brand-surface p-4 sm:p-5"><h4 className="mb-3 font-black text-brand-900">{academic.focus}</h4><ul className="m-0 list-none space-y-2 p-0 text-sm font-semibold text-gray-600 sm:space-y-2.5">{academic.items.map((item) => <li className="flex items-center" key={item}><i className="ph-fill ph-check-circle mr-2 text-lg text-brand-accent sm:mr-2.5" />{item}</li>)}</ul></div></div><Link className="inline-flex items-center rounded-xl border border-gray-200 bg-brand-surface px-6 py-3 text-sm font-bold text-brand-900 shadow-sm transition-colors hover:bg-brand-900 hover:text-white sm:px-8 sm:py-3.5 sm:text-base" href={academic.route}>View Full Syllabus<i className="ph-bold ph-arrow-right ml-2" /></Link></div></div> : null}
          </div>
        </section>

        <section id="gallery" className="scroll-mt-24 bg-brand-surface py-16 sm:py-24">
          <div className="mx-auto max-w-[1400px] px-4 sm:px-6"><div className="mb-8 flex flex-col items-center justify-center gap-5 sm:mb-12 md:flex-row md:items-end md:justify-between"><div className="text-center md:text-left"><h2 className="mb-2 text-2xl font-black text-brand-900 sm:text-3xl lg:text-4xl xl:text-5xl">{gallerySection?.title}</h2><p className="text-sm font-medium text-gray-500 sm:text-base lg:text-lg">{gallerySection?.summary}</p></div><Link className="flex items-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-brand-900 shadow-sm transition hover:text-brand-accent lg:px-6 lg:py-3.5 lg:text-base" href="/gallery">Open Full Gallery<i className="ph-bold ph-arrow-right ml-2" /></Link></div>
            <div className="grid auto-rows-[200px] grid-cols-1 gap-4 sm:auto-rows-[250px] sm:gap-6 md:auto-rows-[300px] md:grid-cols-4">{gallery?.slice(0, 3).map((album, index) => <GalleryTile className={index === 0 ? 'md:col-span-2 md:row-span-2' : index === 1 ? 'md:col-span-2' : ''} image={album.cover || '/images/school-hero.jpg'} icon={index === 0 ? 'ph-buildings' : index === 1 ? 'ph-basketball' : undefined} title={album.name} key={album.name} />)}<Link className="flex flex-col items-center justify-center rounded-[2rem] border-4 border-brand-surface bg-brand-900 p-4 text-center transition-colors hover:border-brand-accent sm:p-6" href="/gallery"><i className="ph-fill ph-images mb-3 text-3xl text-brand-accent sm:mb-4 sm:text-4xl lg:text-5xl" /><h3 className="mb-1 text-lg font-black text-white sm:mb-2 sm:text-xl">View All Albums</h3><p className="text-xs font-medium text-gray-400 sm:text-sm">Explore student life.</p></Link></div>
          </div>
        </section>

        <section id="admissions" className="relative z-20 mx-auto -mb-24 max-w-[1200px] scroll-mt-24 px-4 sm:-mb-32 sm:px-6 lg:-mb-40"><div className="relative overflow-hidden rounded-[2rem] border border-white/10 bg-brand-900 p-8 text-center shadow-[0_30px_60px_-15px_rgba(20,59,89,.4)] sm:rounded-[2.5rem] sm:p-10 md:p-16 lg:rounded-[3rem] lg:p-20"><h2 className="relative z-10 mb-4 text-2xl font-black text-white sm:mb-6 sm:text-3xl md:text-4xl lg:text-5xl">{admissions?.title}</h2><p className="relative z-10 mx-auto mb-8 max-w-2xl text-sm font-medium leading-relaxed text-gray-300 sm:mb-10 sm:text-base md:text-lg lg:text-xl">{admissions?.summary || admissions?.content}</p><div className="relative z-10 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-5"><Link className="w-full rounded-2xl bg-brand-accent px-8 py-3 text-base font-black text-brand-900 shadow-accent-glow transition-transform hover:-translate-y-1 hover:bg-yellow-500 sm:w-auto sm:px-10 sm:py-4 sm:text-lg" href="/admissions/information">Admissions Inquiry</Link><Link className="w-full rounded-2xl border border-white/20 bg-white/10 px-8 py-3 text-base font-black text-white backdrop-blur-md transition-all hover:bg-white hover:text-brand-900 sm:w-auto sm:px-10 sm:py-4 sm:text-lg" href="/contact">Contact Administration</Link></div></div></section>
      </main>
    </LandingLayout>
  );
}

function ParentAction({ href, icon, tone, title, subtitle, external = false }: { href: string; icon: string; tone: 'red' | 'navy' | 'blue' | 'green'; title: string; subtitle: string; external?: boolean }) {
  const tones = { red: 'bg-red-50 text-brand-red', navy: 'bg-brand-surface text-brand-900', blue: 'bg-blue-50 text-blue-600', green: 'bg-emerald-50 text-emerald-600' };
  const content = <><span className={`mb-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-base shadow-sm transition-transform duration-300 group-hover:scale-110 sm:mb-1.5 sm:h-10 sm:w-10 sm:rounded-xl sm:text-lg lg:mb-2 lg:h-12 lg:w-12 lg:text-2xl xl:mb-0 ${tones[tone]}`}><i className={`ph-fill ${icon}`} /></span><span><strong className="block text-[8px] font-black leading-[1.08] text-brand-900 sm:text-[11px] lg:text-sm">{title}</strong><small className="mt-0.5 hidden text-[10px] font-bold uppercase text-gray-500 lg:block">{subtitle}</small></span></>;
  const classes = 'group flex min-w-0 flex-col items-center rounded-xl border border-transparent bg-transparent p-1.5 text-center transition-all duration-300 hover:border-gray-100 hover:bg-brand-surface sm:rounded-2xl sm:p-2 lg:p-4 xl:flex-row xl:space-x-4 xl:text-left';
  return external ? <a className={classes} href={href}>{content}</a> : <Link className={classes} href={href}>{content}</Link>;
}

function Fact({ label, value }: { label: string; value: string }) {
  return <div className="min-w-0"><span className="mb-1 block text-[8px] font-bold uppercase leading-tight text-gray-400 sm:text-xs">{label}</span><strong className="block text-lg font-black text-brand-900 sm:text-2xl">{value}</strong></div>;
}

function GalleryTile({ image, icon, title, className = '' }: { image: string; icon?: string; title: string; className?: string }) {
  return <Link className={`group relative overflow-hidden rounded-[2rem] shadow-soft ${className}`} href="/gallery"><img className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" src={image} alt="" /><div className="absolute inset-0 bg-gradient-to-t from-brand-900/90 via-brand-900/20 to-transparent" /><div className="absolute bottom-6 left-6 right-6 sm:bottom-8 sm:left-8 sm:right-8">{icon ? <span className="mb-3 flex h-10 w-10 items-center justify-center rounded-2xl border border-white/30 bg-white/20 text-white backdrop-blur sm:mb-4 sm:h-12 sm:w-12"><i className={`ph-fill ${icon} text-xl sm:text-2xl`} /></span> : null}<h3 className="text-lg font-black leading-tight text-white sm:text-2xl lg:text-3xl">{title}</h3></div></Link>;
}
