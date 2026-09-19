<x-admin.layouts.app title="System">
    <div class="grid max-w-4xl gap-6">
        <section class="adm-card p-6">
            <h2 class="font-display text-base font-semibold">Status</h2>
            <dl class="mt-4 divide-y divide-zinc-100 text-sm">
                @foreach ($info as $label => $value)
                    <div class="flex justify-between py-2"><dt class="text-zinc-500">{{ $label }}</dt><dd class="font-medium">{{ $value }}</dd></div>
                @endforeach
            </dl>
        </section>

        <section class="adm-card p-6">
            <h2 class="font-display text-base font-semibold">Maintenance</h2>
            <p class="mt-1 text-sm text-zinc-500">The server has no SSH, so run these after each upload. <strong>Deploy</strong> runs migrations and rebuilds caches.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                @foreach ($actions as $action)
                    <form method="POST" action="{{ route('admin.system.run') }}">
                        @csrf
                        <input type="hidden" name="action" value="{{ $action }}">
                        <button class="{{ $action === 'deploy' ? 'adm-btn' : 'adm-btn-secondary' }}">{{ \Illuminate\Support\Str::headline($action) }}</button>
                    </form>
                @endforeach
            </div>
            @if (session('ops_output'))
                <pre class="mt-4 max-h-96 overflow-auto rounded-md bg-ink p-4 text-xs text-zinc-100">@foreach (session('ops_output') as $cmd => $out)$ php artisan {{ $cmd }}
{{ $out }}

@endforeach</pre>
            @endif
        </section>
    </div>
</x-admin.layouts.app>
