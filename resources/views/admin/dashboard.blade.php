@php use App\Models\Lead; @endphp
<x-admin.layouts.app title="Dashboard">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.leads.index', ['status' => 'new']) }}" class="adm-card block p-5 hover:border-ink">
            <p class="text-sm text-zinc-500">New leads</p>
            <p class="mt-1 font-display text-3xl font-semibold text-ink">{{ $newLeads }}</p>
        </a>
        <div class="adm-card p-5">
            <p class="text-sm text-zinc-500">Leads this month</p>
            <p class="mt-1 font-display text-3xl font-semibold">{{ $leadsThisMonth }}</p>
        </div>
    </div>

    <section class="adm-card mt-6">
        <h2 class="border-b border-zinc-200 px-5 py-3 font-display text-base font-semibold">Latest leads</h2>
        <ul class="divide-y divide-zinc-100 text-sm">
            @forelse ($recent as $lead)
                <li><a href="{{ route('admin.leads.show', $lead) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-zinc-50">
                    <span class="w-28 shrink-0 text-zinc-500">{{ $lead->created_at->diffForHumans(short: true) }}</span>
                    <span class="flex-1 font-medium">{{ $lead->name }}@if ($lead->company) <span class="font-normal text-zinc-500">· {{ $lead->company }}</span>@endif</span>
                    <span class="hidden text-zinc-500 sm:inline">{{ Lead::TYPES[$lead->type] ?? $lead->type }}</span>
                    <x-admin.lead-status :status="$lead->status" />
                </a></li>
            @empty
                <li class="px-5 py-8 text-center text-zinc-500">No leads yet. They'll appear here once the site's forms are live.</li>
            @endforelse
        </ul>
    </section>
</x-admin.layouts.app>
