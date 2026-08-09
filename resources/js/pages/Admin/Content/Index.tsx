import { Head, request } from '@/runtime';
import { useMemo, useState } from 'react';
import AdminLayout from '../../../layouts/AdminLayout';

type OptionMap = Record<string, string>;
type Field = { name: string; label: string; type: string; required: boolean; options: OptionMap; help?: string | null };
type Definition = { title: string; singular: string; description: string; titleField: string; statusField?: string | null; lockedField?: string | null; singleton: boolean; fields: Field[] };
type Props = { resource: string; definition: Definition; records: Record<string, any>[] };

function blankRecord(definition: Definition) {
  return Object.fromEntries(definition.fields.map((field) => {
    if (field.type === 'checkbox') return [field.name, false];
    if (field.type === 'number') return [field.name, 0];
    if (field.type === 'lines' || field.type === 'sections') return [field.name, []];
    if (field.type === 'social') return [field.name, { facebook: '', instagram: '', youtube: '' }];
    if (field.type === 'select') return [field.name, Object.keys(field.options)[0] ?? ''];
    return [field.name, ''];
  }));
}

function editableRecord(record: Record<string, any>, definition: Definition) {
  const editable = { ...blankRecord(definition), ...record };
  definition.fields.forEach((field) => {
    if (field.type === 'datetime-local' && editable[field.name]) editable[field.name] = String(editable[field.name]).slice(0, 16);
    if (field.type === 'date' && editable[field.name]) editable[field.name] = String(editable[field.name]).slice(0, 10);
    if ((field.type === 'lines' || field.type === 'sections') && !Array.isArray(editable[field.name])) editable[field.name] = [];
    if (field.type === 'social' && (!editable[field.name] || Array.isArray(editable[field.name]))) editable[field.name] = { facebook: '', instagram: '', youtube: '' };
  });
  return editable;
}

function mediaUrl(path?: string) {
  if (!path) return '';
  return path.startsWith('http') || path.startsWith('/') ? path : `/storage/${path}`;
}

export default function ContentIndex({ resource, definition, records: initialRecords }: Props) {
  const initialSelection = definition.singleton && initialRecords[0] ? editableRecord(initialRecords[0], definition) : blankRecord(definition);
  const [records, setRecords] = useState(initialRecords);
  const [data, setData] = useState<Record<string, any>>(initialSelection);
  const [selectedId, setSelectedId] = useState<number | null>(definition.singleton ? initialRecords[0]?.id ?? null : null);
  const [files, setFiles] = useState<Record<string, File | null>>({});
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [message, setMessage] = useState('');
  const [processing, setProcessing] = useState(false);
  const [search, setSearch] = useState('');
  const selectedRecord = useMemo(() => records.find((record) => record.id === selectedId), [records, selectedId]);
  const visibleRecords = useMemo(() => records.filter((record) => JSON.stringify(record).toLowerCase().includes(search.toLowerCase())), [records, search]);
  const resourceIcon: Record<string, string> = { settings: 'ph-buildings', 'hero-slides': 'ph-images-square', navigation: 'ph-list-dashes', pages: 'ph-file-text', programs: 'ph-books', people: 'ph-users-three', notices: 'ph-bell-ringing', news: 'ph-newspaper', events: 'ph-calendar-dots', facilities: 'ph-buildings', gallery: 'ph-images', documents: 'ph-download-simple', popups: 'ph-notification', careers: 'ph-briefcase' };

  const refresh = async () => {
    const payload = await request<Props>(`/api/v1/admin/content/${resource}`);
    setRecords(payload.records);
    if (selectedId) {
      const updated = payload.records.find((record) => record.id === selectedId);
      if (updated) setData(editableRecord(updated, definition));
    }
  };

  const beginNew = () => {
    setSelectedId(null); setData(blankRecord(definition)); setFiles({}); setErrors({}); setMessage('');
  };

  const beginEdit = (record: Record<string, any>) => {
    setSelectedId(record.id); setData(editableRecord(record, definition)); setFiles({}); setErrors({}); setMessage('');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const submit = async (event: React.FormEvent) => {
    event.preventDefault(); setProcessing(true); setErrors({}); setMessage('');
    const body = new FormData();
    definition.fields.forEach((field) => {
      if (field.type === 'image' || field.type === 'file') {
        if (files[field.name]) body.append(field.name, files[field.name] as File);
      } else if (field.type === 'checkbox') body.append(field.name, data[field.name] ? '1' : '0');
      else if (field.type === 'lines' || field.type === 'sections' || field.type === 'social') body.append(field.name, JSON.stringify(data[field.name] ?? (field.type === 'social' ? {} : [])));
      else if (data[field.name] !== null && data[field.name] !== undefined) body.append(field.name, String(data[field.name]));
    });
    if (selectedId) body.append('_method', 'PATCH');

    try {
      const response = await request<{ message: string }>(`/api/v1/admin/content/${resource}${selectedId ? `/${selectedId}` : ''}`, { method: 'POST', body });
      setMessage(response.message); await refresh();
      if (!selectedId && !definition.singleton) { setData(blankRecord(definition)); setFiles({}); }
    } catch (error: any) {
      const nextErrors: Record<string, string> = {};
      Object.entries(error.errors ?? {}).forEach(([key, value]) => { nextErrors[key] = Array.isArray(value) ? String(value[0]) : String(value); });
      setErrors(Object.keys(nextErrors).length ? nextErrors : { form: error.message ?? 'The record could not be saved.' });
    } finally { setProcessing(false); }
  };

  const remove = async () => {
    if (!selectedId || definition.singleton || !window.confirm(`Delete this ${definition.singular.toLowerCase()}? This cannot be undone.`)) return;
    setProcessing(true); setErrors({});
    try {
      const response = await request<{ message: string }>(`/api/v1/admin/content/${resource}/${selectedId}`, { method: 'DELETE' });
      setMessage(response.message); setSelectedId(null); setData(blankRecord(definition)); await refresh();
    } catch (error: any) { setErrors({ form: error.message ?? 'The record could not be deleted.' }); }
    finally { setProcessing(false); }
  };

  const change = (name: string, value: any) => setData((current) => ({ ...current, [name]: value }));

  return <AdminLayout><Head title={definition.title} />
    <header className="admin-page-header cms-page-header"><div className="cms-title-block"><span className="cms-title-icon"><i className={`ph-fill ${resourceIcon[resource] || 'ph-file-text'}`} /></span><div><span className="admin-kicker">Website content</span><h1>{definition.title}</h1><p>{definition.description}</p></div></div>{!definition.singleton ? <div className="admin-page-actions"><button className="button primary" type="button" onClick={beginNew}><i className="ph-bold ph-plus" /> New {definition.singular.toLowerCase()}</button></div> : null}</header>
    <div className="cms-workspace">
      <section className="admin-panel cms-editor">
        <div className="admin-panel-heading"><div><h2>{selectedId ? `Edit ${definition.singular.toLowerCase()}` : `New ${definition.singular.toLowerCase()}`}</h2><span>{selectedId ? `Record #${selectedId}` : 'Complete the fields below'}</span></div>{selectedId && !definition.singleton ? <button className="cms-delete" type="button" onClick={remove} disabled={processing}><i className="ph ph-trash" /> Delete</button> : null}</div>
        {message ? <div className="cms-message success"><i className="ph-fill ph-check-circle" />{message}</div> : null}
        {errors.form ? <div className="cms-message error">{errors.form}</div> : null}
        <form className="admin-form cms-form" onSubmit={submit}>
          {definition.fields.map((field) => <FieldEditor key={field.name} field={field} value={data[field.name]} record={selectedRecord} error={errors[field.name]} onChange={(value) => change(field.name, value)} onFile={(file) => setFiles((current) => ({ ...current, [field.name]: file }))} />)}
          <div className="cms-form-actions"><button className="button primary" disabled={processing}>{processing ? 'Saving...' : selectedId ? 'Save changes' : `Create ${definition.singular.toLowerCase()}`}</button>{selectedId && !definition.singleton ? <button className="button secondary" type="button" onClick={beginNew}>Cancel editing</button> : null}</div>
        </form>
      </section>

      {!definition.singleton ? <aside className="admin-panel cms-library"><div className="admin-panel-heading"><div><h2>Content library</h2><span>{records.length} {records.length === 1 ? 'record' : 'records'}</span></div></div>{records.length > 5 ? <label className="cms-search"><i className="ph ph-magnifying-glass" /><input value={search} onChange={(event) => setSearch(event.target.value)} placeholder={`Search ${definition.title.toLowerCase()}`} /></label> : null}<div className="cms-records">{visibleRecords.map((record, index) => { const locked = Boolean(definition.lockedField && record[definition.lockedField]); return <button className={`${record.id === selectedId ? 'is-selected' : ''} ${locked ? 'is-locked' : ''}`} type="button" onClick={() => beginEdit(record)} disabled={locked} title={locked ? 'Primary navigation is protected' : `Edit ${record[definition.titleField] || definition.singular}`} key={record.id}><span className="cms-record-index">{String(index + 1).padStart(2, '0')}</span><span className="cms-record-copy"><strong>{record[definition.titleField] || `Untitled ${definition.singular}`}</strong><small>{locked ? 'Default primary navigation' : record.slug || record.category || record.group_name || record.menu_group || `Record #${record.id}`}</small></span>{locked ? <span className="cms-status is-primary">Primary</span> : definition.statusField ? <Status value={record[definition.statusField]} /> : null}<i className={`ph ${locked ? 'ph-lock-key' : 'ph-pencil-simple'} cms-edit-icon`} /></button>; })}{!visibleRecords.length ? <div className="cms-empty"><i className="ph ph-folder-open" /><strong>{records.length ? 'No matching content' : 'No content yet'}</strong><span>{records.length ? 'Try a different search.' : `Create the first ${definition.singular.toLowerCase()} using the editor.`}</span></div> : null}</div></aside> : null}
    </div>
  </AdminLayout>;
}

function Status({ value }: { value: any }) {
  const active = value === true || value === 1 || value === 'published' || value === 'active' || value === 'upcoming' || value === 'ongoing';
  return <span className={`cms-status ${active ? 'is-live' : ''}`}>{typeof value === 'boolean' ? (value ? 'Live' : 'Hidden') : String(value ?? 'Draft')}</span>;
}

function FieldEditor({ field, value, record, error, onChange, onFile }: { field: Field; value: any; record?: Record<string, any>; error?: string; onChange: (value: any) => void; onFile: (file: File | null) => void }) {
  if (field.type === 'checkbox') return <label className="cms-check"><input type="checkbox" checked={Boolean(value)} onChange={(event) => onChange(event.target.checked)} /><span><strong>{field.label}</strong>{field.help ? <small>{field.help}</small> : null}</span></label>;
  if (field.type === 'sections') return <SectionsEditor field={field} value={value ?? []} error={error} onChange={onChange} />;
  if (field.type === 'social') return <div className="cms-field cms-span-2"><span className="cms-label">{field.label}</span><div className="cms-social-grid">{['facebook', 'instagram', 'youtube'].map((network) => <label key={network}><span>{network}</span><input value={value?.[network] ?? ''} onChange={(event) => onChange({ ...(value ?? {}), [network]: event.target.value })} placeholder={`https://${network}.com/...`} /></label>)}</div></div>;
  if (field.type === 'lines') return <label className="cms-field cms-span-2"><span className="cms-label">{field.label}{field.required ? ' *' : ''}</span><textarea rows={5} value={Array.isArray(value) ? value.join('\n') : ''} onChange={(event) => onChange(event.target.value.split('\n').filter((line) => line.trim()).map((line) => line.trim()))} placeholder="One item per line" />{field.help ? <small>{field.help}</small> : null}{error ? <em>{error}</em> : null}</label>;
  if (field.type === 'image' || field.type === 'file') {
    const currentPath = record?.[field.name];
    return <label className="cms-field cms-span-2"><span className="cms-label">{field.label}{field.required && !record ? ' *' : ''}</span><span className="cms-upload"><i className={`ph ${field.type === 'image' ? 'ph-image' : 'ph-paperclip'}`} /><span><strong>Choose {field.type === 'image' ? 'an image' : 'a file'}</strong><small>{currentPath ? 'Selecting a new file will replace the current one.' : field.type === 'image' ? 'JPG, PNG or WebP up to 5 MB.' : 'Upload up to 15 MB.'}</small></span><input type="file" accept={field.type === 'image' ? 'image/*' : undefined} onChange={(event) => onFile(event.target.files?.[0] ?? null)} /></span>{field.type === 'image' && currentPath ? <img className="cms-image-preview" src={mediaUrl(currentPath)} alt="Current upload" /> : currentPath ? <a className="cms-current-file" href={mediaUrl(currentPath)} target="_blank" rel="noreferrer"><i className="ph ph-file" /> View current file</a> : null}{error ? <em>{error}</em> : null}</label>;
  }
  if (field.type === 'select') return <label className="cms-field"><span className="cms-label">{field.label}{field.required ? ' *' : ''}</span><select value={value ?? ''} onChange={(event) => onChange(event.target.value)}>{Object.entries(field.options).map(([optionValue, label]) => <option value={optionValue} key={optionValue}>{label}</option>)}</select>{error ? <em>{error}</em> : null}</label>;
  if (field.type === 'textarea') return <label className="cms-field cms-span-2"><span className="cms-label">{field.label}{field.required ? ' *' : ''}</span><textarea rows={6} value={value ?? ''} onChange={(event) => onChange(event.target.value)} />{field.help ? <small>{field.help}</small> : null}{error ? <em>{error}</em> : null}</label>;
  return <label className="cms-field"><span className="cms-label">{field.label}{field.required ? ' *' : ''}</span><input type={field.type === 'datetime-local' || field.type === 'date' || field.type === 'number' || field.type === 'email' ? field.type : 'text'} value={value ?? ''} onChange={(event) => onChange(field.type === 'number' ? Number(event.target.value) : event.target.value)} />{field.help ? <small>{field.help}</small> : null}{error ? <em>{error}</em> : null}</label>;
}

function SectionsEditor({ field, value, error, onChange }: { field: Field; value: Array<{ title: string; body: string }>; error?: string; onChange: (value: any) => void }) {
  const sections = Array.isArray(value) ? value : [];
  const update = (index: number, key: 'title' | 'body', next: string) => onChange(sections.map((section, sectionIndex) => sectionIndex === index ? { ...section, [key]: next } : section));
  return <div className="cms-field cms-span-2"><div className="cms-sections-heading"><span className="cms-label">{field.label}</span><button type="button" onClick={() => onChange([...sections, { title: '', body: '' }])}><i className="ph ph-plus" /> Add section</button></div>{field.help ? <small>{field.help}</small> : null}<div className="cms-sections">{sections.map((section, index) => <div className="cms-section-row" key={index}><span>{String(index + 1).padStart(2, '0')}</span><div><input value={section.title ?? ''} onChange={(event) => update(index, 'title', event.target.value)} placeholder="Section heading" /><textarea rows={4} value={section.body ?? ''} onChange={(event) => update(index, 'body', event.target.value)} placeholder="Section content" /></div><button type="button" onClick={() => onChange(sections.filter((_, sectionIndex) => sectionIndex !== index))} aria-label="Remove section"><i className="ph ph-x" /></button></div>)}</div>{error ? <em>{error}</em> : null}</div>;
}
