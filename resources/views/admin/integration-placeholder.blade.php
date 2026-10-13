@extends('layouts.admin', ['title' => $title])

@section('content')
    <section class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-7 shadow-sm shadow-slate-200/30 sm:p-10">
        <span class="grid size-12 place-items-center rounded-2xl bg-indigo-50 text-xl text-indigo-700">↗</span>
        <p class="mt-6 text-xs font-semibold uppercase tracking-[0.15em] text-indigo-600">Integration ready</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight">{{ $title }}</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">{{ $message }}</p>
        <div class="mt-7 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
            <p class="text-sm font-medium text-slate-700">No local records are being used</p>
            <p class="mt-1 text-sm leading-6 text-slate-500">This section is a protected placeholder. Connect the corresponding commerce API through a dedicated client and service to display live data.</p>
        </div>
        <a class="mt-7 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-800" href="{{ route('admin.dashboard') }}">← Return to dashboard</a>
    </section>
@endsection
