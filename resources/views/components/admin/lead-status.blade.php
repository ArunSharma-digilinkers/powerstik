@props(['status'])
<span @class([
    'inline-block rounded-full px-2 py-0.5 text-xs font-medium',
    'bg-brand-100 text-brand-800' => $status === 'new',
    'bg-amber-100 text-amber-800' => in_array($status, ['contacted', 'quoted']),
    'bg-green-100 text-green-800' => $status === 'won',
    'bg-zinc-100 text-zinc-600' => in_array($status, ['lost', 'spam']),
])>{{ \App\Models\Lead::STATUSES[$status] ?? $status }}</span>
