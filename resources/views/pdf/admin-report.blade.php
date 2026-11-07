@php($habits = \App\Enums\Habit::cases())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan 7 KAIH</title>
    @include('pdf._styles')
</head>
<body>
    @include('pdf._header')

    <p class="title">Laporan Pemantauan <span class="accent">7 Kebiasaan Anak Indonesia Hebat</span></p>
    <p class="muted" style="margin: 2px 0 10px">
        Cakupan: {{ $selectedClass ? 'Kelas '.$selectedClass->name : ($filters['grade'] ? 'Tingkat '.$filters['grade'] : 'Seluruh kelas X, XI, XII') }}
        @if ($filters['attempt']) · Pengisian ke-{{ $filters['attempt'] }} @endif
        @if ($filters['search']) · Pencarian “{{ $filters['search'] }}” @endif
    </p>

    <table class="kpi">
        <tr>
            <td><div class="lbl">Total Pengisian</div><div class="num">{{ $summary['submissions'] }}</div></td>
            <td><div class="lbl">Siswa Berpartisipasi</div><div class="num">{{ $summary['respondents'] }}</div></td>
            <td><div class="lbl">Rata-rata Skor</div><div class="num">{{ $summary['average'] !== null ? number_format($summary['average'], 2) : '–' }}</div></td>
            <td><div class="lbl">Kategori</div><div class="num" style="font-size: 13px">{{ $summary['average'] !== null ? \App\Models\Submission::levelFor($summary['average'])['label'] : '–' }}</div></td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 12px">
        <tr>
            <td style="width: 58%; vertical-align: top; padding-right: 10px">
                <table class="data">
                    <thead><tr><th>Kebiasaan</th><th style="width: 45%">Capaian</th><th class="right">Skor</th></tr></thead>
                    <tbody>
                        @foreach ($habitAverages as $row)
                            <tr>
                                <td><b>{{ $row['habit']->label() }}</b></td>
                                <td style="padding-top: 8px"><div class="bar-bg"><div class="bar" style="width: {{ ($row['average'] ?? 0) / 4 * 100 }}%; background: {{ $row['habit']->hex() }}"></div></div></td>
                                <td class="right"><b>{{ $row['average'] !== null ? number_format($row['average'], 2) : '–' }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 42%; vertical-align: top">
                <table class="data">
                    <thead><tr><th>Pengisian</th><th class="right">Respon</th><th class="right">Rata-rata</th></tr></thead>
                    <tbody>
                        @foreach ($trend as $point)
                            <tr><td>Ke-{{ $point['attempt'] }}</td><td class="right">{{ $point['responses'] }}</td><td class="right"><b>{{ $point['average'] !== null ? number_format($point['average'], 2) : '–' }}</b></td></tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="box" style="margin-top: 8px">
                    @foreach ($levels as $level)
                        <span class="pill" style="background: {{ $level['color'] }}">{{ $level['label'] }}: {{ $level['count'] }}</span>
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>
    @include('pdf._header')
    <p class="title" style="font-size: 14px; margin-bottom: 8px">Rincian Pengisian Siswa</p>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th class="center">Ke-</th>
                <th>Tanggal</th>
                @foreach ($habits as $habit)
                    <th class="center" style="font-size: 8px">{{ $habit->label() }}</th>
                @endforeach
                <th class="center">Rata²</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($submissions as $submission)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td><b>{{ $submission->user->name }}</b></td>
                    <td>{{ $submission->schoolClass?->name ?? '-' }}</td>
                    <td class="center">{{ $submission->attempt_number }}</td>
                    <td>{{ $submission->submitted_at->format('d/m/Y') }}</td>
                    @foreach ($habits as $habit)
                        @php($value = $submission->answers->where('habit', $habit)->avg('score'))
                        <td class="center">{{ $value !== null ? number_format($value, 1) : '–' }}</td>
                    @endforeach
                    <td class="center"><b>{{ number_format($submission->average_score, 2) }}</b></td>
                    <td><span class="pill" style="background: {{ $submission->level()['color'] }}">{{ $submission->level()['label'] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="{{ 7 + count($habits) }}" class="center muted">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign">
        <tr>
            <td></td>
            <td>Jayapura, {{ now()->translatedFormat('d F Y') }}<br>Kepala Sekolah<br><br><br><br>(..............................)</td>
        </tr>
    </table>
</body>
</html>
