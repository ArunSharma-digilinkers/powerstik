<x-admin.layouts.guest title="Log in">
    <div class="flex min-h-full items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            <img src="/images/brand/logo.png" alt="Powerstik" class="mx-auto h-12 w-auto">
            <form method="POST" action="{{ route('admin.login') }}" class="adm-card mt-8 space-y-5 p-6">
                @csrf
                <x-admin.field name="email" label="Email" type="email" autocomplete="username" required autofocus />
                <x-admin.field name="password" label="Password" type="password" autocomplete="current-password" required />
                <label class="flex items-center gap-2 text-sm text-zinc-600">
                    <input type="checkbox" name="remember" class="rounded border-zinc-300 text-brand-500"> Remember me
                </label>
                <button class="adm-btn w-full justify-center">Log in</button>
            </form>
        </div>
    </div>
</x-admin.layouts.guest>
