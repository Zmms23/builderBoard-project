<x-filament-panels::page.simple>
    <div class="mx-auto w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
        <div class="mb-8 text-center">
            <div class="mb-4 flex justify-center">
                <img
                    src="{{ filament()->getBrandLogo() ?? asset('images/download.png') }}"
                    alt="{{ config('app.name') }} logo"
                    style="height: 2rem"
                    class="fi-logo"
                >
            </div>

            <h1 class="text-3xl font-semibold text-white">Sign in</h1>
            <p class="mt-2 text-sm text-gray-400">Use your account to open the tenant dashboard.</p>
        </div>

        @if (session('plain_login_error'))
            <div class="mb-5 rounded-2xl border border-danger-600/30 bg-danger-500/10 px-4 py-3 text-sm text-danger-200">
                {{ session('plain_login_error') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/admin/login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="local-email" class="mb-2 block text-sm font-medium text-white">Email address</label>
                <input
                    id="local-email"
                    type="email"
                    name="email"
                    value="{{ old('email', '') }}"
                    required
                    autofocus
                    class="fi-input block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-gray-500"
                >
            </div>

            <div>
                <label for="local-password" class="mb-2 block text-sm font-medium text-white">Password</label>
                <input
                    id="local-password"
                    type="password"
                    name="password"
                    required
                    class="fi-input block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-gray-500"
                >
            </div>

            <label class="flex items-center gap-3 text-sm text-gray-300">
                <input type="checkbox" name="remember" value="1" class="rounded border-white/15 bg-white/5">
                <span>Remember me</span>
            </label>

            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-500"
            >
                Sign in
            </button>
        </form>

        <div class="mt-6 rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-sm text-gray-400">
            Demo accounts: <span class="font-medium text-white">admin@test.com</span>,
            <span class="font-medium text-white">manager@test.com</span>,
            <span class="font-medium text-white">worker@test.com</span><br>
            Password: <span class="font-medium text-white">password</span>
        </div>
    </div>
</x-filament-panels::page.simple>
