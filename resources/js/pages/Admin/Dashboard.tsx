import { Head, Link } from '@/runtime';
import AdminLayout from '../../layouts/AdminLayout';

type Metric = { label: string; value: number };
type Module = Record<string, Array<{ label: string; href: string }>>;

export default function Dashboard({ metrics, modules }: { metrics: Metric[]; modules: Module }) {
  return <AdminLayout><Head title="Dashboard" /><header className="admin-page-header"><div><span className="admin-kicker">Overview</span><h1>Good morning, school office.</h1><p>Here is the current state of your school website.</p></div><div className="admin-page-actions"><Link className="button secondary" href="/"><i className="ph-bold ph-arrow-square-out" /><span>Public website</span></Link><Link className="button primary" href="/admin/content/notices"><i className="ph-bold ph-plus" /><span>New notice</span></Link></div></header><section className="admin-metrics">{metrics.map((metric, index) => <article key={metric.label}><span className="metric-index">0{index + 1}</span><strong>{metric.value}</strong><span className="metric-label">{metric.label}</span><small>{index === 1 ? 'Needs attention' : 'Updated just now'}</small></article>)}</section><section className="admin-section-heading"><div><span className="admin-kicker">Content areas</span><h2>Website modules</h2></div><span className="admin-section-note">Manage content by department</span></section><div className="admin-module-grid">{Object.entries(modules).map(([module, items]) => <article className="admin-module" key={module}><div className="admin-module-top"><span>{module}</span><i className="ph-bold ph-arrow-right" aria-hidden="true" /></div>{items.map((item) => <Link href={item.href} key={item.label}>{item.label}<i className="ph ph-caret-right" /></Link>)}</article>)}</div></AdminLayout>;
}
