<?php

namespace App\Enums;

/**
 * 7 Kebiasaan Anak Indonesia Hebat (7 KAIH).
 */
enum Habit: string
{
    case BangunPagi = 'bangun_pagi';
    case Beribadah = 'beribadah';
    case Berolahraga = 'berolahraga';
    case MakanSehat = 'makan_sehat';
    case GemarBelajar = 'gemar_belajar';
    case Bermasyarakat = 'bermasyarakat';
    case TidurCepat = 'tidur_cepat';

    public function label(): string
    {
        return match ($this) {
            self::BangunPagi => 'Bangun Pagi',
            self::Beribadah => 'Beribadah',
            self::Berolahraga => 'Berolahraga',
            self::MakanSehat => 'Makan Sehat & Bergizi',
            self::GemarBelajar => 'Gemar Belajar',
            self::Bermasyarakat => 'Bermasyarakat',
            self::TidurCepat => 'Tidur Cepat',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::BangunPagi => '🌅',
            self::Beribadah => '🙏',
            self::Berolahraga => '🏃',
            self::MakanSehat => '🥗',
            self::GemarBelajar => '📚',
            self::Bermasyarakat => '🤝',
            self::TidurCepat => '🌙',
        };
    }

    public function tagline(): string
    {
        return match ($this) {
            self::BangunPagi => 'Memulai hari dengan disiplin dan semangat.',
            self::Beribadah => 'Bertumbuh dalam hati yang takut akan Tuhan.',
            self::Berolahraga => 'Tubuh sehat, pikiran segar, siap berprestasi.',
            self::MakanSehat => 'Merawat tubuh sebagai anugerah Tuhan.',
            self::GemarBelajar => 'Haus ilmu sesuai tuntutan perkembangan zaman.',
            self::Bermasyarakat => 'Mengasihi sesama dan menghargai ciptaan Tuhan.',
            self::TidurCepat => 'Istirahat cukup untuk mental yang sehat.',
        };
    }

    /**
     * The Kalam Kudus Jayapura mission statement this habit supports.
     */
    public function mission(): string
    {
        return match ($this) {
            self::BangunPagi => 'Misi 3 · Bertanggung jawab & berbudi pekerti luhur',
            self::Beribadah => 'Misi 1 · Memiliki hati yang takut akan Tuhan',
            self::Berolahraga => 'Misi 3 · Manusia yang sehat',
            self::MakanSehat => 'Misi 3 · Manusia yang sehat',
            self::GemarBelajar => 'Misi 4 & 5 · Pengetahuan & keterampilan berkualitas',
            self::Bermasyarakat => 'Misi 2 · Mengasihi sesama & menghargai alam ciptaan Tuhan',
            self::TidurCepat => 'Misi 3 · Sehat mental & bertanggung jawab',
        };
    }

    /**
     * Gradient classes used for the habit's card header.
     */
    public function gradient(): string
    {
        return match ($this) {
            self::BangunPagi => 'from-amber-400 to-orange-500',
            self::Beribadah => 'from-indigo-500 to-violet-600',
            self::Berolahraga => 'from-emerald-400 to-teal-600',
            self::MakanSehat => 'from-lime-400 to-green-600',
            self::GemarBelajar => 'from-sky-400 to-blue-600',
            self::Bermasyarakat => 'from-rose-400 to-pink-600',
            self::TidurCepat => 'from-slate-600 to-indigo-900',
        };
    }

    /**
     * Solid hex color for PDF output (DomPDF has no gradient support).
     */
    public function hex(): string
    {
        return match ($this) {
            self::BangunPagi => '#f59e0b',
            self::Beribadah => '#6366f1',
            self::Berolahraga => '#10b981',
            self::MakanSehat => '#65a30d',
            self::GemarBelajar => '#0ea5e9',
            self::Bermasyarakat => '#f43f5e',
            self::TidurCepat => '#334155',
        };
    }
}
