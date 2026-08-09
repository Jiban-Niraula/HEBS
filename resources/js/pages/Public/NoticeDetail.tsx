import PublicPageFrame from '../../components/public/PublicPageFrame';
import { Link } from '@/runtime';

type Notice = { title: string; category: string; priority: string; summary?: string; content?: string; published_at?: string };

export default function NoticeDetail({ notice }: { notice: Notice }) {
  return <PublicPageFrame title={notice.title} eyebrow={`Notice · ${notice.category}`}><article className="container article-page"><div className="article-meta">{notice.priority} · {notice.published_at ? new Date(notice.published_at).toLocaleDateString() : 'Official update'}</div><p className="page-summary">{notice.summary}</p><div className="article-body"><p>{notice.content}</p></div><Link className="text-link" href="/updates/notices">← Back to notices</Link></article></PublicPageFrame>;
}
