@php($habits = \App\Enums\Habit::cases())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap 7 KAIH – {{ $user->name }}</title>
    @include('pdf._styles')
</head>
<body>
    @include('pdf._header')

    <p class="title">Rekap Perkembangan <span class="accent">7 KAIH</span></p>
    <p class="muted" style="margin: 2px 0 10px">{{ $user->name }} · {{ $user->email }} · Kelas {{ $user->schoolClass?->name ?? '-' }} · {{ $submissions->count() }}/{{ $maxSubmissions }} pengisian</p>

    <table class="data">
        <thead>
            <tr>
                <th>Kebiasaan</th>
                @foreach ($submissions as $submission)
                    <th class="center">#{{ $submission->attempt_number }}<br><span style="font-weight: normal; text-transform: none">{{ $submission->submitted_at->format('d/m') }}</span></th>
                @endforeach
                <th class="center">Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($habits as $habit)
                @php($values = $submissions->map(fn ($submission) => $submission->answers->where('habit', $habit)->avg('score')))
                <tr>
                    <td style="color: {{ $habit->hex() }}"><b>{{ $habit->label() }}</b></td>
                    @foreach ($values as $value)
                        <td class="center">{{ $value !== null ? number_format($value, 2) : '–' }}</td>
                    @endforeach
                    <td class="center"><b>{{ $values->filter(fn ($value) => $value !== null)->isNotEmpty() ? number_format($values->filter(fn ($value) => $value !== null)->avg(), 2) : '–' }}</b></td>
                </tr>
            @endforeach
            <tr>
                <td><b>Rata-rata Keseluruhan</b></td>
                @foreach ($submissions as $submission)
                    <td class="center"><b>{{ number_format($submission->average_score, 2) }}</b></td>
                @endforeach
                <td class="center"><b>{{ $submissions->isNotEmpty() ? number_format($submissions->avg('average_score'), 2) : '–' }}</b></td>
            </tr>
            <tr>
                <td>Kategori</td>
                @foreach ($submissions as $submission)
                    <td class="center"><span class="pill" style="background: {{ $submission->level()['color'] }}">{{ $submission->level()['label'] }}</span></td>
                @endforeach
                <td></td>
            </tr>
        </tbody>
    </table>

    @if ($submissions->whereNotNull('reflection')->isNotEmpty())
        <div class="box" style="margin-top: 12px">
            <b>Refleksi:</b>
            @foreach ($submissions->whereNotNull('reflection') as $submission)
                <div style="margin-top: 4px">#{{ $submission->attempt_number }} — <i>“{{ $submission->reflection }}”</i></div>
            @endforeach
        </div>
    @endif

    <table class="sign">
        <tr>
            <td>Mengetahui,<br>Wali Kelas<br><br><br><br>(..............................)</td>
            <td>Jayapura, {{ now()->translatedFormat('d F Y') }}<br>Orang Tua / Wali<br><br><br><br>(..............................)</td>
        </tr>
    </table>
</body>
</html>
