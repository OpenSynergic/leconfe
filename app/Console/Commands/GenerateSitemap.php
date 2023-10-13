<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\StaticPage;
use Illuminate\Console\Command;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!file_exists(public_path('sitemap_index.xml'))) {
            SitemapIndex::create()
                ->add('/website-sitemap.xml')
                ->add('/announcements-sitemap.xml')
                ->add('/static_pages-sitemap.xml')
                ->writeToFile(public_path('sitemap_index.xml'));
        }

        if (!file_exists(public_path('website-sitemap.xml'))) {
            Sitemap::create()
                ->add('/')
                ->add('/about')
                ->add('/contact')
                ->add('/current')
                ->add('/register')
                ->add('/login')
                ->add('/current/announcements')
                ->writeToFile(public_path('website-sitemap.xml'));
        }
        
        Sitemap::create()
            ->add(Announcement::with(['conference'])->get())
            ->writeToFile(public_path('announcements-sitemap.xml'));
        
        Sitemap::create()
            ->add(StaticPage::with(['conference'])->get())
            ->writeToFile(public_path('static_page-sitemap.xml'));
    }
}
