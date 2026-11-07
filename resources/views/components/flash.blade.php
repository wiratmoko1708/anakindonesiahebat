@foreach (['status' => 'bg-emerald-50 text-emerald-800 ring-emerald-200', 'error' => 'bg-rose-50 text-rose-800 ring-rose-200'] as $key => $classes)
    @if (session($key))
        <div class="relative z-10 mb-4 flex animate-fade-up items-start justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium shadow-lg ring-1 {{ $classes }}" data-dismissible role="status">
            <span>{{ session($key) }}</span>
            <button type="button" class="opacity-60 hover:opacity-100" data-dismiss aria-label="Tutup">✕</button>
        </div>
    @endif
@endforeach
