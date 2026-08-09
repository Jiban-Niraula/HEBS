import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';

type Post = { title: string; category?: string; summary?: string; content?: string; published_at?: string };
export default function NewsDetail({ post }: { post: Post }) { return <PublicPageFrame title={post.title} eyebrow={`News · ${post.category ?? 'School community'}`}><article className="container article-page"><div className="article-meta">{post.published_at ? new Date(post.published_at).toLocaleDateString() : 'School news'}</div><p className="page-summary">{post.summary}</p><div className="article-body"><p>{post.content}</p></div><Link className="text-link" href="/updates/news">← Back to news</Link></article></PublicPageFrame>; }
