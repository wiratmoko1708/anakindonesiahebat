<?php

namespace Database\Seeders;

use App\Enums\Habit;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Instrumen 7 KAIH yang diselaraskan dengan visi & misi
     * Sekolah Kristen Kalam Kudus Jayapura.
     *
     * @var array<string, list<string>>
     */
    public const QUESTIONS = [
        'bangun_pagi' => [
            'Saya bangun pagi sebelum pukul 05.30 tanpa harus dibangunkan berulang kali.',
            'Saya merapikan tempat tidur dan menyiapkan diri sehingga tiba di sekolah tepat waktu.',
        ],
        'beribadah' => [
            'Saya berdoa secara pribadi (pagi, sebelum makan, dan sebelum tidur).',
            'Saya membaca Alkitab / renungan harian atau beribadah sesuai keyakinan saya.',
        ],
        'berolahraga' => [
            'Saya berolahraga atau melakukan aktivitas fisik minimal 30 menit dalam sehari.',
            'Saya memilih bergerak aktif (berjalan, bersepeda, bermain) daripada lama duduk dengan gawai.',
        ],
        'makan_sehat' => [
            'Saya sarapan sebelum memulai kegiatan di sekolah.',
            'Saya makan sayur dan buah serta membatasi makanan cepat saji dan minuman manis.',
        ],
        'gemar_belajar' => [
            'Saya belajar atau mengulang pelajaran di rumah minimal 1 jam selain mengerjakan PR.',
            'Saya membaca buku atau mencari pengetahuan dan keterampilan baru atas kemauan sendiri.',
        ],
        'bermasyarakat' => [
            'Saya menerapkan budaya S.M.A.R.T (senyum, salam, sapa, sopan, maaf, antre, tolong, terima kasih).',
            'Saya membantu orang lain atau ikut menjaga kebersihan lingkungan rumah, sekolah, dan sekitar.',
        ],
        'tidur_cepat' => [
            'Saya tidur sebelum pukul 22.00 sehingga istirahat cukup 8–9 jam.',
            'Saya berhenti menggunakan gawai minimal 30 menit sebelum tidur.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sortOrder = 0;

        foreach (Habit::cases() as $habit) {
            foreach (self::QUESTIONS[$habit->value] as $body) {
                Question::updateOrCreate(
                    ['habit' => $habit->value, 'body' => $body],
                    ['sort_order' => ++$sortOrder, 'is_active' => true],
                );
            }
        }
    }
}
