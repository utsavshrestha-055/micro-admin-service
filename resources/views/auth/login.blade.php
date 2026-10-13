<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section class="relative hidden overflow-hidden bg-indigo-700 px-14 py-16 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-28 -top-24 size-[34rem] rounded-full border border-white/10"></div>
            <div class="absolute -right-10 -top-6 size-[26rem] rounded-full border border-white/10"></div>
            <div class="relative flex items-center gap-3">
                <span class="grid size-11 place-items-center rounded-xl bg-white text-sm font-bold text-indigo-700">CA</span>
                <span class="font-semibold tracking-tight">Commerce Admin</span>
            </div>
            <div class="relative max-w-xl pb-14">
                <p class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Operations · Access · Control</p>
                <h1 class="text-5xl font-semibold leading-[1.1] tracking-tight">A clear view of your commerce platform.</h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-indigo-100">Manage access from one secure workspace, with operational modules ready to connect as your services come online.</p>
            </div>
            <p class="relative text-xs text-indigo-200">Protected administrator access · Identity managed by your auth service</p>
        </section>
        <main class="flex min-h-screen items-center justify-center bg-slate-50 px-5 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="grid size-10 place-items-center rounded-xl bg-indigo-600 text-sm font-bold text-white">CA</span>
                    <span class="font-semibold">Commerce Admin</span>
                </div>
                <p class="text-sm font-semibold text-indigo-600">Welcome back</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight">Sign in to your account</h2>
                <p class="mt-2 text-sm text-slate-500">Use your administrator credentials to continue.</p>

                @if (session('status'))
                    <div role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}" class="form-input" placeholder="you@company.com">
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="form-input" placeholder="Enter your password">
                    </div>
                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Sign in securely</button>
                </form>
                <p class="mt-8 text-center text-xs text-slate-400">Your access token stays on this server and is stored in an encrypted session.</p>
            </div>
        </main>
    </div>
</body>
</html>
