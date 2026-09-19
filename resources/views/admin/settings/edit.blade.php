<x-admin.layouts.app title="Settings">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @foreach ($groups as $group)
            <section class="adm-card p-6">
                <h2 class="font-display text-base font-semibold">{{ $group['label'] }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach ($group['fields'] as $key => $field)
                        <div @class(['sm:col-span-2' => ($field['type'] ?? 'text') === 'textarea'])>
                            <x-admin.field :name="$key" :label="$field['label']" :type="$field['type'] ?? 'text'"
                                           :value="$values[$key] ?? ''" :help="$field['help'] ?? null" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
        <button class="adm-btn">Save settings</button>
    </form>
</x-admin.layouts.app>
