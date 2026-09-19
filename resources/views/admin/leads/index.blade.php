@php use App\Models\Lead; @endphp
<x-admin.layouts.app title="Leads">
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, company, email, phone…" class="adm-input max-w-xs">
        <select name="type" class="adm-input w-auto" onchange="this.form.submit()">
            <option value="">All types</option>
            @foreach (Lead::TYPES as $key => $label)
                <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}@if ($counts[$key] ?? 0) ({{ $counts[$key] }} new)@endif</option>
            @endforeach
        </select>
        <select name="status" class="adm-input w-auto" onchange="this.form.submit()">
            <option value="">All statuses (excl. spam)</option>
            @foreach (Lead::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="assigned" class="adm-input w-auto" onchange="this.form.submit()">
            <option value="">Anyone</option>
            @foreach ($staff as $id => $name)
                <option value="{{ $id }}" @selected(request('assigned') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        <a href="{{ route('admin.leads.export', request()->query()) }}" class="adm-btn-secondary ml-auto">Export CSV</a>
    </form>

    <div class="adm-card overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-50 text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase">
                <tr><th class="px-4 py-3">Received</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Company</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Assigned</th></tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($leads as $lead)
                    <tr @class(['hover:bg-zinc-50', 'font-semibold' => $lead->status === 'new'])>
                        <td class="px-4 py-2.5 whitespace-nowrap text-zinc-500">{{ $lead->created_at->format('d M, H:i') }}</td>
                        <td class="px-4 py-2.5">{{ Lead::TYPES[$lead->type] ?? $lead->type }}@if ($lead->files_count) <span title="Has attachments">📎</span>@endif</td>
                        <td class="px-4 py-2.5"><a href="{{ route('admin.leads.show', $lead) }}" class="hover:text-brand-600">{{ $lead->name }}</a></td>
                        <td class="px-4 py-2.5">{{ $lead->company ?? '—' }}</td>
                        <td class="px-4 py-2.5 font-normal">{{ $lead->phone }}<br><span class="text-zinc-500">{{ $lead->email }}</span></td>
                        <td class="px-4 py-2.5"><x-admin.lead-status :status="$lead->status" /></td>
                        <td class="px-4 py-2.5 font-normal">{{ $lead->assignee?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-zinc-500">No leads match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leads->links() }}</div>
</x-admin.layouts.app>
