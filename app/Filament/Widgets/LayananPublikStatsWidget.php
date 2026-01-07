<?php

namespace App\Filament\Widgets;

use App\Models\Pengaduan;
use App\Models\PermohonanInformasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LayananPublikStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $pengaduanBaru = Pengaduan::where('status', 'baru')->count();
        $ppidBaru = PermohonanInformasi::where('status', 'diterima')->count();

        return [
            Stat::make('Pengaduan Baru', $pengaduanBaru)
                ->description('Menunggu ditangani')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color($pengaduanBaru > 0 ? 'danger' : 'success'),

            Stat::make('Permohonan PPID', $ppidBaru)
                ->description('Permohonan informasi baru')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color($ppidBaru > 0 ? 'warning' : 'success'),

            Stat::make('Total Pengaduan', Pengaduan::count())
                ->description('Seluruh pengaduan')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('gray'),

            Stat::make('Total PPID', PermohonanInformasi::count())
                ->description('Seluruh permohonan')
                ->icon('heroicon-o-document-text')
                ->color('gray'),
        ];
    }
}
