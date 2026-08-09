import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';

type Post = { id: number; title: string; slug: string; category?: string; summary?: string; published_at?: string };
type Paginator = { data: Post[]; links: Array<{ url?: string; label: string; active: boolean }> };

export default function News({ posts }: { posts: Paginator }) {
  return <PublicPageFrame title="News" eyebrow="Updates"><section className="container listing-page"><p className="page-summary">Stories from classrooms, activities, school programs, and the wider community.</p><div className="news-list">{posts.data.map((post) => <Link className="news-list-row" href={`/updates/news/${post.slug}`} key={post.id}><div className="news-date">{post.published_at ? new Date(post.published_at).toLocaleDateString() : 'News'}</div><div><span>{post.category}</span><h2>{post.title}</h2><p>{post.summary}</p></div><b>Read →</b></Link>)}</div></section></PublicPageFrame>;
}
