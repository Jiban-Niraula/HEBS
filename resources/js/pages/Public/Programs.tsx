import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';
import type { AcademicProgram } from '../../types/school';

export default function Programs({ programs }: { programs: AcademicProgram[] }) {
  return <PublicPageFrame title="Academic programs" eyebrow="Academics"><section className="container listing-page"><p className="page-summary">A connected pathway from early learning to higher secondary study, with each stage shaped around the learner.</p><div className="listing-table">{programs.map((program, index) => <Link className="listing-row" href={`/academics/${program.slug}`} key={program.slug}><span>{String(index + 1).padStart(2, '0')}</span><div><h2>{program.name}</h2><p>{program.description ?? program.short_description}</p></div><small>{program.range ?? program.grade_or_age_range}</small><b>View program →</b></Link>)}</div></section></PublicPageFrame>;
}
