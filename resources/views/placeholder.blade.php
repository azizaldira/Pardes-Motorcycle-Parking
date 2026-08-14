@extends('layouts.app')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); text-align: center;">
        <i class="fas fa-tools" style="font-size: 4rem; margin-bottom: 1rem; color: var(--border);"></i>
        <h2 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 0.5rem;">Dalam Pengembangan</h2>
        <p>{{ $message ?? 'Fitur ini sedang dalam tahap pengembangan dan akan segera hadir.' }}</p>
    </div>
@endsection
