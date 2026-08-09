import PublicPageFrame from '../../components/public/PublicPageFrame';

type Facility = { id: number; name: string; category?: string; description?: string; image_path?: string };

const mediaUrl = (path: string) => path.startsWith('http') || path.startsWith('/') ? path : `/storage/${path}`;

export default function Facilities({ facilities }: { facilities: Facility[] }) {
  return <PublicPageFrame title="Facilities" eyebrow="School Life"><section className="container listing-page"><p className="page-summary">Spaces and resources that support teaching, activity, reading, collaboration, and student life.</p><div className="facility-grid">{facilities.map((facility) => <article key={facility.id}>{facility.image_path ? <img className="facility-image" src={mediaUrl(facility.image_path)} alt={facility.name} /> : null}<span className="eyebrow">{facility.category}</span><h2>{facility.name}</h2><p>{facility.description}</p></article>)}</div></section></PublicPageFrame>;
}
