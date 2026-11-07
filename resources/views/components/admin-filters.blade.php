@props(['filters', 'classes', 'action', 'showSearch' => false, 'showAttempt' => false])
<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'card flex flex-wrap items-end gap-3 p-4']) }}>
    <div class="w-full sm:w-auto">
        <label class="label" for="filter-grade">Tingkat</label>
        <select id="filter-grade" name="grade" class="input sm:w-32" data-autosubmit>
            <option value="">Semua</option>
            @foreach (['X', 'XI', 'XII'] as $grade)
                <option value="{{ $grade }}" @selected($filters['grade'] === $grade)>Kelas {{ $grade }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-full sm:w-auto">
        <label class="label" for="filter-class">Kelas</label>
        <select id="filter-class" name="school_class_id" class="input sm:w-40" data-autosubmit>
            <option value="">Semua kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($filters['school_class_id'] === $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    @if ($showAttempt)
        <div class="w-full sm:w-auto">
            <label class="label" for="filter-attempt">Pengisian</label>
            <select id="filter-attempt" name="attempt" class="input sm:w-32" data-autosubmit>
                <option value="">Semua</option>
                @for ($attempt = 1; $attempt <= config('kaih.max_submissions'); $attempt++)
                    <option value="{{ $attempt }}" @selected($filters['attempt'] === $attempt)>Ke-{{ $attempt }}</option>
                @endfor
            </select>
        </div>
    @endif
    @if ($showSearch)
        <div class="min-w-48 flex-1">
            <label class="label" for="filter-search">Cari siswa</label>
            <input id="filter-search" type="search" name="search" value="{{ $filters['search'] }}" class="input" placeholder="Nama atau email…">
        </div>
    @endif
    <div class="flex gap-2">
        <button class="btn-primary">Terapkan</button>
        @if (array_filter($filters))
            <a href="{{ $action }}" class="btn-ghost">Reset</a>
        @endif
    </div>
    {{ $slot }}
</form>
