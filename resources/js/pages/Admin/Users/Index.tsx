import { Head, request } from '@/runtime';
import { useState } from 'react';
import AdminLayout from '../../../layouts/AdminLayout';

type User = { id: number; name: string; email: string; role: 'administrator' | 'editor'; is_active: boolean; created_at: string };
const blank = { name: '', email: '', role: 'editor' as const, password: '', is_active: true };

export default function UsersIndex({ users: initialUsers, currentUserId }: { users: User[]; currentUserId: number }) {
  const [users, setUsers] = useState(initialUsers);
  const [selectedId, setSelectedId] = useState<number | null>(null);
  const [data, setData] = useState<Record<string, any>>(blank);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [message, setMessage] = useState('');
  const [processing, setProcessing] = useState(false);

  const refresh = async () => setUsers((await request<{ users: User[] }>('/api/v1/admin/users')).users);
  const startNew = () => { setSelectedId(null); setData(blank); setErrors({}); setMessage(''); };
  const edit = (user: User) => { setSelectedId(user.id); setData({ name: user.name, email: user.email, role: user.role, password: '', is_active: user.is_active }); setErrors({}); setMessage(''); window.scrollTo({ top: 0, behavior: 'smooth' }); };
  const submit = async (event: React.FormEvent) => {
    event.preventDefault(); setProcessing(true); setErrors({}); setMessage('');
    try {
      const response = await request<{ message: string }>(`/api/v1/admin/users${selectedId ? `/${selectedId}` : ''}`, { method: selectedId ? 'PATCH' : 'POST', body: JSON.stringify(data) });
      setMessage(response.message); await refresh(); if (!selectedId) setData(blank);
    } catch (error: any) {
      const next: Record<string, string> = {};
      Object.entries(error.errors ?? {}).forEach(([key, value]) => { next[key] = Array.isArray(value) ? String(value[0]) : String(value); });
      setErrors(Object.keys(next).length ? next : { form: error.message || 'Unable to save this account.' });
    } finally { setProcessing(false); }
  };
  const remove = async () => {
    if (!selectedId || selectedId === currentUserId || !confirm('Delete this administrator account?')) return;
    try { const response = await request<{ message: string }>(`/api/v1/admin/users/${selectedId}`, { method: 'DELETE' }); setMessage(response.message); startNew(); await refresh(); }
    catch (error: any) { setErrors({ form: error.message || 'Unable to delete this account.' }); }
  };

  return <AdminLayout><Head title="Administrator users" /><header className="admin-page-header cms-page-header"><div><span className="admin-kicker">Access control</span><h1>Administrator users</h1><p>Create and maintain the accounts allowed to manage school content and operations.</p></div><div className="admin-page-actions"><button className="button primary" type="button" onClick={startNew}><i className="ph-bold ph-user-plus" /> Add administrator</button></div></header><div className="cms-workspace"><section className="admin-panel cms-editor"><div className="admin-panel-heading"><div><h2>{selectedId ? 'Edit account' : 'New account'}</h2><span>{selectedId === currentUserId ? 'Your signed-in account' : 'Access details and permissions'}</span></div>{selectedId && selectedId !== currentUserId ? <button className="cms-delete" type="button" onClick={remove}><i className="ph ph-trash" /> Delete</button> : null}</div>{message ? <div className="cms-message success"><i className="ph-fill ph-check-circle" />{message}</div> : null}{errors.form ? <div className="cms-message error">{errors.form}</div> : null}<form className="admin-form cms-form" onSubmit={submit}><UserField label="Full name" value={data.name} error={errors.name} onChange={(value) => setData({ ...data, name: value })} /><UserField label="Email address" type="email" value={data.email} error={errors.email} onChange={(value) => setData({ ...data, email: value })} /><label className="cms-field"><span className="cms-label">Role *</span><select value={data.role} onChange={(event) => setData({ ...data, role: event.target.value })}><option value="administrator">Administrator</option><option value="editor">Content editor</option></select><small>Administrators can manage user accounts. Editors manage website content.</small></label><UserField label={selectedId ? 'New password' : 'Password'} type="password" value={data.password} error={errors.password} help={selectedId ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.'} onChange={(value) => setData({ ...data, password: value })} /><label className="cms-check cms-span-2"><input type="checkbox" checked={data.is_active} onChange={(event) => setData({ ...data, is_active: event.target.checked })} disabled={selectedId === currentUserId} /><span><strong>Account active</strong><small>Inactive accounts cannot sign in.</small></span></label><div className="cms-form-actions"><button className="button primary" disabled={processing}>{processing ? 'Saving...' : selectedId ? 'Save account' : 'Create account'}</button>{selectedId ? <button className="button secondary" type="button" onClick={startNew}>Cancel</button> : null}</div></form></section><aside className="admin-panel cms-library"><div className="admin-panel-heading"><div><h2>Authorized accounts</h2><span>{users.length} users</span></div></div><div className="cms-records admin-user-list">{users.map((user) => <button className={selectedId === user.id ? 'is-selected' : ''} type="button" onClick={() => edit(user)} key={user.id}><span className="admin-user-avatar">{user.name.split(' ').map((part) => part[0]).slice(0, 2).join('')}</span><span className="cms-record-copy"><strong>{user.name}{user.id === currentUserId ? ' (You)' : ''}</strong><small>{user.email} · {user.role}</small></span><span className={`cms-status ${user.is_active ? 'is-live' : ''}`}>{user.is_active ? 'Active' : 'Disabled'}</span><i className="ph ph-pencil-simple cms-edit-icon" /></button>)}</div></aside></div></AdminLayout>;
}

function UserField({ label, value, onChange, error, type = 'text', help }: { label: string; value: string; onChange: (value: string) => void; error?: string; type?: string; help?: string }) {
  return <label className="cms-field"><span className="cms-label">{label} *</span><input type={type} value={value} onChange={(event) => onChange(event.target.value)} />{help ? <small>{help}</small> : null}{error ? <em>{error}</em> : null}</label>;
}
