<?php

namespace App\Filament\Widgets;

use App\Models\Berita;
use App\Models\Pengumuman;
use App\Models\Agenda;
use App\Models\Dokumen;
use App\Models\Pengaduan;
use App\Models\PermohonanInformasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Berita', Berita::count())
                ->description('Artikel dipublikasikan')
                ->icon('heroicon-o-newspaper')
                ->color('success'),

            Stat::make('Pengumuman Aktif', Pengumuman::where('status', 'published')->count())
                ->description('Pengumuman terpublikasi')
                ->icon('heroicon-o-megaphone')
                ->color('warning'),

            Stat::make('Agenda Mendatang', Agenda::where('start_date', '>=', now())->count())
                ->description('Event yang akan datang')
                ->icon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Dokumen Publik', Dokumen::where('is_public', true)->count())
                ->description('Dokumen dapat diunduh')
                ->icon('heroicon-o-document')
                ->color('primary'),
        ];
    }
}
