import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';

type Notice = { id: number; title: string; slug: string; category: string; priority: string; summary?: string; published_at?: string };
type Paginator = { data: Notice[]; links: Array<{ url?: string; label: string; active: boolean }> };

export default function Notices({ notices }: { notices: Paginator }) {
  return <PublicPageFrame title="Notices" eyebrow="Updates"><section className="container listing-page"><p className="page-summary">Official notices from the school office, including academic, admission, examination, holiday, and administrative updates.</p><div className="notice-list">{notices.data.map((notice) => <Link className="notice-list-row" href={`/updates/notices/${notice.slug}`} key={notice.id}><time>{notice.published_at ? new Date(notice.published_at).toLocaleDateString() : 'Official notice'}</time><div><span>{notice.category} · {notice.priority}</span><h2>{notice.title}</h2><p>{notice.summary}</p></div><b>Read →</b></Link>)}</div><div className="pagination">{notices.links.map((link) => link.url ? <Link className={link.active ? 'active' : ''} href={link.url} key={link.label} dangerouslySetInnerHTML={{ __html: link.label }} /> : <span key={link.label} dangerouslySetInnerHTML={{ __html: link.label }} />)}</div></section></PublicPageFrame>;
}
