import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';

type EventItem = { title: string; venue?: string; starts_at: string; ends_at?: string; description?: string; map_url?: string };
export default function EventDetail({ event }: { event: EventItem }) { return <PublicPageFrame title={event.title} eyebrow="School event"><article className="container article-page"><div className="event-callout"><strong>{new Date(event.starts_at).toLocaleDateString('en-GB', { dateStyle: 'full' })}</strong><span>{new Date(event.starts_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })} · {event.venue}</span></div><p className="page-summary">{event.description}</p>{event.map_url ? <a className="text-link" href={event.map_url}>Open venue map →</a> : null}<br /><Link className="text-link" href="/updates/events">← Back to events</Link></article></PublicPageFrame>; }
