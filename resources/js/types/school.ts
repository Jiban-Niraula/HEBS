export type SchoolIdentity = {
  name: string;
  shortName?: string;
  motto?: string;
  establishedYear?: string;
  address: string;
  phone: string;
  email: string;
  officeHours: string;
  appUrl: string;
  mapUrl: string;
  logoUrl?: string;
  socialLinks?: Record<string, string>;
  copyrightText?: string;
};

export type Announcement = {
  label: string;
  text: string;
  href: string;
};

export type PopupNotice = {
  id: string;
  title: string;
  summary: string;
  content: string;
  priority: 'normal' | 'important' | 'urgent' | 'emergency';
  buttonLabel?: string;
  buttonUrl?: string;
  imageUrl?: string;
  displayFrequency: 'every_visit' | 'once_per_session' | 'once_per_day' | 'once_per_notice';
  showCloseButton?: boolean;
  allowDoNotShowAgain?: boolean;
};

export type QuickLink = {
  title: string;
  href: string;
  description: string;
};

export type AcademicProgram = {
  id?: number;
  name: string;
  slug?: string;
  range?: string;
  description?: string;
  grade_or_age_range?: string;
  short_description?: string;
  full_introduction?: string;
  teaching_approach?: string;
  assessment_approach?: string;
  student_support?: string;
  activities?: string[];
  learning_objectives?: string[];
  curriculum_overview?: string[];
  featured_image_path?: string;
};

export type NoticeSummary = {
  title: string;
  date: string;
  category: string;
  priority: string;
  href?: string;
};

export type NewsSummary = {
  title: string;
  date: string;
  summary: string;
  href?: string;
  category?: string;
  image?: string;
};

export type EventSummary = {
  title: string;
  date: string;
  time: string;
  venue: string;
  href?: string;
};
