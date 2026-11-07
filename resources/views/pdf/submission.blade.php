@php($level = $submission->level())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil 7 KAIH – {{ $submission->user->name }}</title>
    @include('pdf._styles')
</head>
<body>
    @include('pdf._header')

    <p class="title">Hasil Kuesioner <span class="accent">7 Kebiasaan Anak Indonesia Hebat</span></p>
    <p class="muted" style="margin-top: 2px">Pengisian ke-{{ $submission->attempt_number }} dari {{ config('kaih.max_submissions') }}</p>

    <table class="box" style="width: 100%">
        <tr>
            <td style="width: 70%">
                <table>
                    <tr><td class="muted" style="width: 90px">Nama</td><td><b>{{ $submission->user->name }}</b></td></tr>
                    <tr><td class="muted">Email</td><td>{{ $submission->user->email }}</td></tr>
                    <tr><td class="muted">Kelas</td><td><b>{{ $submission->schoolClass?->name ?? '-' }}</b></td></tr>
                    <tr><td class="muted">Waktu Isi</td><td>{{ $submission->submitted_at->translatedFormat('l, d F Y · H:i') }} WIT</td></tr>
                </table>
            </td>
            <td class="center" style="width: 30%; border-left: 1px solid #e2e8f0">
                <div class="muted" style="font-size: 9px; text-transform: uppercase">Skor Rata-rata</div>
                <div style="font-size: 30px; font-weight: bold; color: {{ $level['color'] }}">{{ number_format($submission->average_score, 2) }}</div>
                <span class="pill" style="background: {{ $level['color'] }}">{{ $level['label'] }}</span>
            </td>
        </tr>
    </table>

    <table class="data" style="margin-bottom: 12px">
        <thead><tr><th style="width: 30%">Kebiasaan</th><th>Capaian</th><th class="right" style="width: 60px">Skor</th><th style="width: 110px">Kategori</th></tr></thead>
        <tbody>
            @foreach ($habitScores as $row)
                @php($habitLevel = \App\Models\Submission::levelFor($row['average']))
                <tr>
                    <td><b>{{ $row['habit']->label() }}</b></td>
                    <td style="padding-top: 8px"><div class="bar-bg"><div class="bar" style="width: {{ $row['average'] / 4 * 100 }}%; background: {{ $row['habit']->hex() }}"></div></div></td>
                    <td class="right"><b>{{ number_format($row['average'], 2) }}</b></td>
                    <td><span class="pill" style="background: {{ $habitLevel['color'] }}">{{ $habitLevel['label'] }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data">
        <thead><tr><th style="width: 22px">No</th><th style="width: 22%">Kebiasaan</th><th>Pernyataan</th><th style="width: 105px">Jawaban</th></tr></thead>
        <tbody>
            @php($number = 0)
            @foreach ($habitScores as $row)
                @foreach ($row['answers'] as $answer)
                    <tr>
                        <td class="center">{{ ++$number }}</td>
                        <td style="color: {{ $row['habit']->hex() }}"><b>{{ $row['habit']->label() }}</b></td>
                        <td>{{ $answer->question_text }}</td>
                        <td><b>{{ $answer->score }}</b> · {{ $scale[$answer->score]['label'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if ($submission->reflection)
        <div class="box" style="margin-top: 12px"><b>Refleksi siswa:</b> <i>“{{ $submission->reflection }}”</i></div>
    @endif

    <p class="muted" style="margin-top: 8px; font-size: 9px">Skala: 1 = Tidak Pernah · 2 = Kadang-kadang · 3 = Sering · 4 = Selalu. Kategori: ≥3,50 Membudaya · ≥2,75 Berkembang Baik · ≥2,00 Mulai Berkembang · &lt;2,00 Perlu Pendampingan.</p>

    <table class="sign">
        <tr>
            <td>Mengetahui,<br>Wali Kelas<br><br><br><br>(..............................)</td>
            <td>Jayapura, {{ now()->translatedFormat('d F Y') }}<br>Siswa<br><br><br><br>( {{ $submission->user->name }} )</td>
        </tr>
    </table>
</body>
</html>
