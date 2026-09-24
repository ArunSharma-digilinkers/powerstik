@php use App\Models\Lead; @endphp
<x-admin.layouts.app :title="(Lead::TYPES[$lead->type] ?? 'Lead').' #'.$lead->id">
    <div class="mb-4 text-sm"><a href="{{ route('admin.leads.index') }}" class="text-zinc-500 hover:text-ink">← Leads</a></div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="adm-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h2 class="font-display text-xl font-semibold">{{ $lead->name }}</h2>
                        <p class="text-sm text-zinc-500">{{ collect([$lead->company, $lead->city, $lead->country])->filter()->join(' · ') }}</p>
                    </div>
                    <x-admin.lead-status :status="$lead->status" />
                </div>
                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @if ($lead->phone)
                        <a href="tel:{{ $lead->phone }}" class="adm-btn-secondary">📞 {{ $lead->phone }}</a>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $lead->phone) }}" target="_blank" class="adm-btn-secondary">WhatsApp</a>
                    @endif
                    @if ($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="adm-btn-secondary">✉ {{ $lead->email }}</a>
                    @endif
                </div>
                @if ($lead->message)
                    <p class="mt-5 text-sm whitespace-pre-line">{{ $lead->message }}</p>
                @endif
            </section>

            @if ($lead->payload)
                <section class="adm-card p-6">
                    <h3 class="font-display text-base font-semibold">Details</h3>
                    <dl class="mt-3 grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                        @foreach ($lead->payload as $key => $val)
                            <div><dt class="text-zinc-500">{{ \Illuminate\Support\Str::headline($key) }}</dt><dd class="font-medium">{{ is_array($val) ? implode(', ', $val) : ($val ?? '—') }}</dd></div>
                        @endforeach
                    </dl>
                </section>
            @endif

            @if ($lead->files->isNotEmpty())
                <section class="adm-card p-6">
                    <h3 class="font-display text-base font-semibold">Attachments</h3>
                    <ul class="mt-3 space-y-1 text-sm">
                        @foreach ($lead->files as $file)
                            <li><a href="{{ route('admin.leads.file', [$lead, $file]) }}" class="text-ink underline hover:no-underline">{{ $file->original_name }}</a> <span class="text-zinc-500">({{ \Illuminate\Support\Number::fileSize($file->size) }})</span></li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        <div class="space-y-6">
            <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="adm-card space-y-4 p-6">
                @csrf @method('PUT')
                <div>
                    <label class="adm-label" for="status">Status</label>
                    <select id="status" name="status" class="adm-input">
                        @foreach (Lead::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($lead->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="adm-label" for="assigned_to">Coordinator</label>
                    <select id="assigned_to" name="assigned_to" class="adm-input">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $id => $name)
                            <option value="{{ $id }}" @selected($lead->assigned_to == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-admin.field name="notes" label="Internal notes" type="textarea" :value="$lead->notes" />
                <button class="adm-btn w-full justify-center">Save</button>
            </form>

            <section class="adm-card p-6 text-xs text-zinc-500">
                <p>Received {{ $lead->created_at->format('d M Y, H:i') }}</p>
                @if ($lead->source_url)<p class="mt-1 break-all">From {{ $lead->source_url }}</p>@endif
                @if ($lead->utm)<p class="mt-1">UTM: {{ collect($lead->utm)->map(fn ($v, $k) => "$k=$v")->join(', ') }}</p>@endif
                <p class="mt-1">IP {{ $lead->ip }}</p>
            </section>

            @if (auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" x-data="{ sure: false }" class="text-sm">
                    @csrf @method('DELETE')
                    <button type="button" x-show="!sure" @click="sure = true" class="font-medium text-red-600 hover:text-red-800">Delete lead…</button>
                    <span x-show="sure" x-cloak class="flex items-center gap-2">Delete permanently? <button class="adm-btn !py-1">Yes</button><button type="button" class="adm-btn-secondary !py-1" @click="sure = false">No</button></span>
                </form>
            @endif
        </div>
    </div>
</x-admin.layouts.app>
