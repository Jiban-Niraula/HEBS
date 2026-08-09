import PublicPageFrame from '../../components/public/PublicPageFrame';

export default function Achievements() {
  const items = ['District-level quiz competition finalist', 'Inter-school football runner-up', 'Outstanding participation in science exhibition', 'Reading culture campaign across grades'];
  return <PublicPageFrame title="Achievements" eyebrow="School Life"><section className="container listing-page"><p className="page-summary">We recognize the work, participation, and progress of students, teachers, teams, and the wider school community.</p><div className="achievement-list">{items.map((item, index) => <article key={item}><span>0{index + 1}</span><h2>{item}</h2><p>School achievement · Published by the administration</p></article>)}</div></section></PublicPageFrame>;
}
