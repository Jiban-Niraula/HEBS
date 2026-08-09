import PublicPageFrame from '../../components/public/PublicPageFrame';
import { Link } from '@/runtime';

type Page = { eyebrow: string; title: string; summary: string; content?: string; image?: string; sections: Array<{ title: string; body: string }> };

export default function InformationPage({ page }: { page: Page }) {
  return <PublicPageFrame title={page.title} eyebrow={page.eyebrow}><section className="container reading-page">{page.image ? <img className="page-feature-image" src={page.image.startsWith('http') || page.image.startsWith('/') ? page.image : `/storage/${page.image}`} alt={page.title} /> : null}<p className="page-summary">{page.summary}</p>{page.content ? <p className="page-content">{page.content}</p> : null}<div className="reading-grid">{page.sections.map((section, index) => <article key={`${section.title}-${index}`}><span className="section-index">{String(index + 1).padStart(2, '0')}</span><h2>{section.title}</h2><p>{section.body}</p></article>)}</div><Link className="button primary" href="/contact">Talk to the school office</Link></section></PublicPageFrame>;
}
