import PublicPageFrame from '../../components/public/PublicPageFrame';

type Person = { id: number; name: string; position?: string; department?: string; biography?: string; photo_path?: string };

const mediaUrl = (path: string) => path.startsWith('http') || path.startsWith('/') ? path : `/storage/${path}`;

export default function People({ title, people }: { title: string; people: Person[] }) {
  return <PublicPageFrame title={title} eyebrow="Our School"><section className="container people-page"><p className="page-summary">Meet the people who guide learning, school governance, and daily support.</p><div className="people-grid">{people.length ? people.map((person) => <article className="person-row" key={person.id}>{person.photo_path ? <img className="person-photo" src={mediaUrl(person.photo_path)} alt={person.name} /> : <div className="person-initials">{person.name.split(' ').map((part) => part[0]).slice(0, 2).join('')}</div>}<div><span>{person.position ?? person.department}</span><h2>{person.name}</h2><p>{person.biography}</p></div></article>) : <p className="empty-state">Profiles will be published here by the school administration.</p>}</div></section></PublicPageFrame>;
}
