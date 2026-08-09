import { Link } from '@/runtime';
import type { PropsWithChildren } from 'react';
import type { Announcement, SchoolIdentity } from '../types/school';
import LandingLayout from './LandingLayout';

type PublicLayoutProps = PropsWithChildren<{
  school: SchoolIdentity;
  announcement?: Announcement | null;
}>;

export default function PublicLayout({ children, school, announcement }: PublicLayoutProps) {
  return (
    <LandingLayout school={school} footerOverlap={false}>
      {announcement ? (
        <Link className="fixed left-0 right-0 top-[92px] z-[90] flex min-h-10 items-center justify-center gap-2 bg-brand-accent px-4 py-2 text-center text-xs font-bold text-brand-900 lg:top-[138px]" href={announcement.href}>
          <i className="ph-fill ph-megaphone text-base" />
          <strong>{announcement.label}</strong>
          <span>{announcement.text}</span>
          <i className="ph-bold ph-arrow-right" />
        </Link>
      ) : null}
      {children}
    </LandingLayout>
  );
}
