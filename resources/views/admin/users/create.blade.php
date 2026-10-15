@extends('layouts.admin', ['title' => 'Create user'])

@section('content')
    <section class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/30 sm:p-8">
        <p class="text-sm text-slate-500">Identity and access</p>
        <h2 class="mt-1 text-2xl font-semibold tracking-tight">Create a user</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">The account will be created through the authentication service and assigned only the customer role. Sync customer access before creating customer accounts.</p>
        <form class="mt-6 space-y-5" method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="form-label" for="first_name">First name</label><input class="form-input" id="first_name" name="first_name" autocomplete="given-name" required maxlength="100" value="{{ old('first_name') }}"></div>
                <div><label class="form-label" for="last_name">Last name</label><input class="form-input" id="last_name" name="last_name" autocomplete="family-name" maxlength="100" value="{{ old('last_name') }}"></div>
            </div>
            <div><label class="form-label" for="email">Email address</label><input class="form-input" id="email" name="email" type="email" autocomplete="email" required maxlength="255" value="{{ old('email') }}"></div>
            <div><label class="form-label" for="password">Password</label><input class="form-input" id="password" name="password" type="password" autocomplete="new-password" minlength="12" maxlength="1024" required><p class="form-hint">Use at least 12 characters.</p></div>
            <div><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="1024" required></div>
            <div class="flex justify-end gap-3"><a class="secondary-button" href="{{ route('admin.users.index') }}">Cancel</a><button class="primary-button" type="submit">Create user</button></div>
        </form>
    </section>
@endsection
