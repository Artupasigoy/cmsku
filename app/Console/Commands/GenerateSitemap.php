<?php

namespace App\Console\Commands;

use App\Models\Berita;
use App\Models\HalamanStatis;
use App\Models\Pengumuman;
use App\Models\Agenda;
use App\Models\Dokumen;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap for the website';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Homepage
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0));

        // Static pages
        $this->info('Adding static pages...');
        HalamanStatis::where('is_active', true)->each(function ($page) use ($sitemap) {
            $sitemap->add(Url::create("/halaman/{$page->slug}")
                ->setLastModificationDate($page->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        });

        // News/Berita
        $this->info('Adding news articles...');
        Berita::where('status', 'published')->each(function ($berita) use ($sitemap) {
            $sitemap->add(Url::create("/berita/{$berita->slug}")
                ->setLastModificationDate($berita->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7));
        });

        // Announcements
        $this->info('Adding announcements...');
        Pengumuman::where('status', 'published')->each(function ($pengumuman) use ($sitemap) {
            $sitemap->add(Url::create("/pengumuman/{$pengumuman->slug}")
                ->setLastModificationDate($pengumuman->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6));
        });

        // Agenda/Events
        $this->info('Adding agenda/events...');
        Agenda::where('status', 'published')->each(function ($agenda) use ($sitemap) {
            $sitemap->add(Url::create("/agenda/{$agenda->slug}")
                ->setLastModificationDate($agenda->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6));
        });

        // Public Documents
        $this->info('Adding public documents...');
        Dokumen::where('is_public', true)->each(function ($dokumen) use ($sitemap) {
            $sitemap->add(Url::create("/dokumen/{$dokumen->slug}")
                ->setLastModificationDate($dokumen->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.5));
        });

        // Main sections
        $sections = [
            '/berita' => 'Berita',
            '/pengumuman' => 'Pengumuman',
            '/agenda' => 'Agenda',
            '/dokumen' => 'Dokumen',
            '/galeri' => 'Galeri',
            '/layanan/ppid' => 'PPID',
            '/layanan/pengaduan' => 'Pengaduan',
            '/kontak' => 'Kontak',
        ];

        foreach ($sections as $path => $name) {
            $sitemap->add(Url::create($path)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8));
        }

        // Save sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at public/sitemap.xml');

        return Command::SUCCESS;
    }
}
