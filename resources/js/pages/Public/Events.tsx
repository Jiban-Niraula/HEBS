import { Link } from '@/runtime';
import PublicPageFrame from '../../components/public/PublicPageFrame';

type EventItem = { id: number; title: string; slug: string; venue?: string; starts_at: string; description?: string };
type Paginator = { data: EventItem[] };
export default function Events({ events }: { events: Paginator }) { return <PublicPageFrame title="Events" eyebrow="Updates"><section className="container listing-page"><p className="page-summary">Upcoming and ongoing events published by the school office.</p><div className="event-list">{events.data.map((event) => <Link className="event-list-row" href={`/updates/events/${event.slug}`} key={event.id}><time><strong>{new Date(event.starts_at).toLocaleDateString('en-GB', { day: '2-digit' })}</strong><span>{new Date(event.starts_at).toLocaleDateString('en-GB', { month: 'short' })}</span></time><div><h2>{event.title}</h2><p>{event.venue} · {event.description}</p></div><b>Details →</b></Link>)}</div></section></PublicPageFrame>; }
