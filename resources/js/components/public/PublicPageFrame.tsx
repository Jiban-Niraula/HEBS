import { Head, usePage } from '@/runtime';
import LandingLayout from '../../layouts/LandingLayout';
import type { PropsWithChildren } from 'react';
import type { SchoolIdentity } from '../../types/school';

type SharedProps = { school: SchoolIdentity };

export default function PublicPageFrame({ title, eyebrow, children }: PropsWithChildren<{ title: string; eyebrow?: string }>) {
  const { school } = usePage<SharedProps>().props;

  return (
    <LandingLayout school={school} footerOverlap={false}>
      <Head title={`${title} | ${school.name}`} />
      <main className="brand-inner-page inner-page bg-brand-surface">
        <section className="relative overflow-hidden bg-brand-900 pb-14 pt-[132px] text-white sm:pb-16 sm:pt-[148px] lg:pb-20 lg:pt-[200px]">
          <img className="absolute inset-0 h-full w-full object-cover opacity-15" src="/images/school-hero.jpg" alt="" />
          <div className="absolute inset-0 bg-brand-900/80" />
          <div className="container relative z-10">
            <p className="mb-3 flex items-center gap-2 text-xs font-black uppercase text-brand-accent"><i className="ph-fill ph-buildings" />{eyebrow ?? 'Hamro English Boarding School'}</p>
            <h1 className="m-0 max-w-4xl text-3xl font-black leading-tight text-white sm:text-4xl lg:text-5xl">{title}</h1>
          </div>
        </section>
        {children}
      </main>
    </LandingLayout>
  );
}
